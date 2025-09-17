<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WooCommerceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WooCommerceController extends Controller
{
    protected WooCommerceService $wooCommerceService;

    public function __construct(WooCommerceService $wooCommerceService)
    {
        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Show WooCommerce sync dashboard
     */
    public function index()
    {
        return view('admin.woocommerce.index');
    }

    /**
     * Sync all products from WooCommerce
     */
    public function syncAll(Request $request): JsonResponse
    {
        try {
            $results = $this->wooCommerceService->syncAllProducts();
            
            return response()->json([
                'success' => true,
                'message' => 'Sincronizzazione completata',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore durante la sincronizzazione: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test WooCommerce connection
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $products = $this->wooCommerceService->getProducts(['per_page' => 1]);
            
            return response()->json([
                'success' => true,
                'message' => 'Connessione WooCommerce funzionante',
                'data' => [
                    'total_products' => count($products),
                    'sample_product' => $products[0] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore connessione WooCommerce: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sync status
     */
    public function getStatus(Request $request): JsonResponse
    {
        $totalCourses = \App\Models\Course::count();
        $syncedCourses = \App\Models\Course::whereNotNull('woocommerce_id')->count();
        $activeCourses = \App\Models\Course::where('is_active', true)->count();

        return response()->json([
            'data' => [
                'total_courses' => $totalCourses,
                'synced_courses' => $syncedCourses,
                'active_courses' => $activeCourses,
                'sync_percentage' => $totalCourses > 0 ? round(($syncedCourses / $totalCourses) * 100, 2) : 0
            ]
        ]);
    }

    /**
     * Handle webhook from WooCommerce
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature if configured
            if (config('woocommerce.webhook_secret')) {
                $signature = $request->header('X-WC-Webhook-Signature');
                $payload = $request->getContent();
                $expectedSignature = hash_hmac('sha256', $payload, config('woocommerce.webhook_secret'));
                
                if (!hash_equals($expectedSignature, $signature)) {
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            $this->wooCommerceService->handleWebhook($request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('WooCommerce webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}
