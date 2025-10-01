<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\CourseEnrollmentNotification;
use App\Notifications\FullVisionAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    /**
     * Handle WooCommerce order webhook
     */
    public function handleOrder(Request $request): JsonResponse
    {
        try {
            // Log the incoming webhook
            Log::info('WooCommerce Order Webhook received', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Validate webhook signature (optional but recommended)
            if (!$this->validateWebhookSignature($request)) {
                Log::warning('Invalid webhook signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $orderData = $request->all();
            
            // Process the order
            $result = $this->processOrder($orderData);
            
            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process WooCommerce order
     */
    protected function processOrder(array $orderData): array
    {
        $wooOrderId = $orderData['id'] ?? null;
        $status = $orderData['status'] ?? null;
        
        // Only process completed/paid orders
        if (!in_array($status, ['completed', 'processing'])) {
            Log::info("Skipping order {$wooOrderId} with status: {$status}");
            return ['skipped' => true, 'reason' => 'Order not completed'];
        }

        // Check if order already processed
        $existingOrder = Order::where('woocommerce_order_id', $wooOrderId)->first();
        if ($existingOrder) {
            Log::info("Order {$wooOrderId} already processed");
            return ['skipped' => true, 'reason' => 'Already processed'];
        }

        $billing = $orderData['billing'] ?? [];
        $lineItems = $orderData['line_items'] ?? [];

        DB::beginTransaction();
        try {
            // Create or find user
            $user = $this->createOrFindUser($billing);
            
            // Create order record
            $order = $this->createOrderRecord($orderData, $user);
            
            // Process line items (courses)
            $enrollments = $this->processLineItems($lineItems, $user, $order);
            
            DB::commit();

            Log::info("Order {$wooOrderId} processed successfully", [
                'user_id' => $user->id,
                'enrollments_count' => count($enrollments)
            ]);

            return [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'enrollments' => $enrollments
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create or find user from billing data
     */
    protected function createOrFindUser(array $billing): User
    {
        $email = $billing['email'] ?? null;
        
        if (!$email) {
            throw new \Exception('No email provided in billing data');
        }

        // Check if user already exists
        $user = User::where('email', $email)->first();
        
        if ($user) {
            Log::info("Found existing user: {$email}");
            return $user;
        }

        // Create new user
        $firstName = $billing['first_name'] ?? '';
        $lastName = $billing['last_name'] ?? '';
        $name = trim($firstName . ' ' . $lastName) ?: $email;
        
        // Generate random password
        $password = Str::random(12);
        
        $user = User::create([
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'source' => 'woocommerce',
        ]);

        // Send welcome email with password
        $user->notify(new UserRegisteredNotification($user, $password));

        Log::info("Created new user: {$email}");

        return $user;
    }

    /**
     * Create order record
     */
    protected function createOrderRecord(array $orderData, User $user): Order
    {
        $total = floatval($orderData['total'] ?? 0);
        $totalCents = intval($total * 100);

        return Order::create([
            'user_id' => $user->id,
            'woocommerce_order_id' => $orderData['id'],
            'total_cents' => $totalCents,
            'currency' => $orderData['currency'] ?? 'EUR',
            'status' => 'paid',
            'gateway' => 'woocommerce',
            'paid_at' => now(),
            'meta_data' => [
                'woocommerce_data' => $orderData,
                'billing' => $orderData['billing'] ?? [],
                'shipping' => $orderData['shipping'] ?? [],
            ]
        ]);
    }

    /**
     * Process line items and create enrollments
     */
    protected function processLineItems(array $lineItems, User $user, Order $order): array
    {
        $enrollments = [];
        $fullVisionCourses = [];

        foreach ($lineItems as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = intval($item['quantity'] ?? 1);

            if (!$productId) {
                continue;
            }

            // Find course by WooCommerce product ID
            $course = Course::where('woocommerce_id', $productId)->first();
            
            if (!$course) {
                Log::warning("Course not found for WooCommerce product ID: {$productId}");
                continue;
            }

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => 'course',
                'item_id' => $course->id,
                'qty' => $quantity,
                'unit_price_cents' => $course->price_cents,
            ]);

            // Check if user is already enrolled
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingEnrollment) {
                Log::info("User {$user->email} already enrolled in course {$course->title}");
                continue;
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $order->id,
                'source' => 'purchase',
                'status' => 'active',
                'started_at' => now(),
                'expires_at' => now()->addYear(), // 1 year access
            ]);

            $enrollments[] = $enrollment;

            // Check if this is Full Vision course
            if ($course->id == 11) {
                $fullVisionCourses[] = $course;
            }
        }

        // Handle Full Vision auto-enrollment
        if (!empty($fullVisionCourses)) {
            $this->handleFullVisionEnrollment($user, $enrollments);
        } else {
            // Send individual course enrollment notifications
            foreach ($enrollments as $enrollment) {
                $user->notify(new CourseEnrollmentNotification($enrollment->course, $enrollment));
            }
        }

        return $enrollments;
    }

    /**
     * Handle Full Vision enrollment
     */
    protected function handleFullVisionEnrollment(User $user, array $enrollments): void
    {
        // Get all active courses except Full Vision
        $allCourses = Course::where('is_active', true)
            ->where('id', '!=', 11)
            ->get();

        $autoEnrolledCourses = [];

        foreach ($allCourses as $course) {
            // Check if user is already enrolled
            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$existingEnrollment) {
                // Auto-enroll in all courses
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'source' => 'full_vision',
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => now()->addYear(),
                ]);

                $autoEnrolledCourses[] = $course;
            }
        }

        // Send Full Vision notification
        if (!empty($autoEnrolledCourses)) {
            $user->notify(new FullVisionAssignedNotification($autoEnrolledCourses));
        }
    }

    /**
     * Validate webhook signature (optional security measure)
     */
    protected function validateWebhookSignature(Request $request): bool
    {
        $secret = config('woocommerce.webhook_secret');
        
        if (!$secret) {
            // If no secret configured, allow all (for development)
            return true;
        }

        $signature = $request->header('X-WC-Webhook-Signature');
        $payload = $request->getContent();
        
        $expectedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));
        
        return hash_equals($expectedSignature, $signature);
    }
}
