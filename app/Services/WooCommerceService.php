<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WooCommerceService
{
    protected $apiUrl;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct()
    {
        $this->apiUrl = config('woocommerce.api_url');
        $this->consumerKey = config('woocommerce.consumer_key');
        $this->consumerSecret = config('woocommerce.consumer_secret');
    }

    /**
     * Sync all products from WooCommerce
     */
    public function syncAllProducts(): array
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0
        ];

        try {
            $products = $this->getProducts();
            
            foreach ($products as $product) {
                try {
                    $result = $this->syncProduct($product);
                    $results[$result]++;
                } catch (\Exception $e) {
                    Log::error('Error syncing product', [
                        'product_id' => $product['id'],
                        'error' => $e->getMessage()
                    ]);
                    $results['errors']++;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching products from WooCommerce', [
                'error' => $e->getMessage()
            ]);
            $results['errors']++;
        }

        return $results;
    }

    /**
     * Get products from WooCommerce API
     */
    public function getProducts(array $params = []): array
    {
        $defaultParams = [
            'per_page' => 100,
            'status' => 'publish',
            // Removed category filter to get all products
        ];

        $params = array_merge($defaultParams, $params);

        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($this->apiUrl . '/products', $params);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch products: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Create a new product in WooCommerce
     */
    public function createProduct(array $productData): array
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->post($this->apiUrl . '/products', $productData);

        if (!$response->successful()) {
            throw new \Exception('Failed to create product: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Sync a single product
     */
    public function syncProduct(array $product): string
    {
        // Check if course already exists
        $course = Course::where('woocommerce_id', $product['id'])->first();

        $courseData = $this->mapProductToCourse($product);

        if ($course) {
            // Update existing course
            $course->update($courseData);
            return 'updated';
        } else {
            // Create new course
            Course::create($courseData);
            return 'created';
        }
    }

    /**
     * Map WooCommerce product to Course model
     */
    protected function mapProductToCourse(array $product): array
    {
        return [
            'woocommerce_id' => $product['id'],
            'title' => $product['name'],
            'slug' => $product['slug'],
            'summary' => $this->extractSummary($product['description']),
            'description' => $product['description'],
            'price_cents' => $this->convertPriceToCents($product['price']),
            'sale_price_cents' => $this->convertPriceToCents($product['sale_price']),
            'thumbnail_url' => $this->downloadAndStoreImage($product['images'][0]['src'] ?? null),
            'gallery' => $this->processGallery($product['images']),
            'status' => $this->mapStatus($product['status']),
            'meta_data' => $this->extractMetaData($product['meta_data']),
            'categories' => $this->mapCategories($product['categories']),
            'tags' => $this->mapTags($product['tags']),
            'stock_status' => $product['stock_status'],
            'manage_stock' => $product['manage_stock'],
            'stock_quantity' => $product['stock_quantity'],
            'is_active' => $product['status'] === 'publish',
            'published_at' => $product['date_created'],
        ];
    }

    /**
     * Extract summary from description (first paragraph)
     */
    protected function extractSummary(string $description): string
    {
        // Remove HTML tags and get first paragraph
        $clean = strip_tags($description);
        $sentences = explode('.', $clean);
        
        return trim($sentences[0] . '.') ?: substr($clean, 0, 200);
    }

    /**
     * Convert price to cents
     */
    protected function convertPriceToCents(?string $price): ?int
    {
        if (!$price) return null;
        
        return (int) round((float) $price * 100);
    }

    /**
     * Download and store product image
     */
    protected function downloadAndStoreImage(?string $imageUrl): ?string
    {
        if (!$imageUrl) return null;

        try {
            $response = Http::get($imageUrl);
            
            if ($response->successful()) {
                $filename = 'courses/' . basename(parse_url($imageUrl, PHP_URL_PATH));
                Storage::disk('public')->put($filename, $response->body());
                
                return $filename;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to download image', [
                'url' => $imageUrl,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Process product gallery
     */
    protected function processGallery(array $images): array
    {
        $gallery = [];
        
        foreach (array_slice($images, 1) as $image) { // Skip first image (featured)
            $filename = $this->downloadAndStoreImage($image['src']);
            if ($filename) {
                $gallery[] = $filename;
            }
        }
        
        return $gallery;
    }

    /**
     * Map WooCommerce status to our status
     */
    protected function mapStatus(string $status): string
    {
        return config('woocommerce.status_mapping')[$status] ?? 'draft';
    }

    /**
     * Extract useful meta data
     */
    protected function extractMetaData(array $metaData): array
    {
        $useful = [];
        
        foreach ($metaData as $meta) {
            $key = $meta['key'];
            $value = $meta['value'];
            
            // Extract useful fields like duration, level, etc.
            if (in_array($key, ['_course_duration', '_course_level', '_course_language'])) {
                $useful[$key] = $value;
            }
        }
        
        return $useful;
    }

    /**
     * Map WooCommerce categories to our categories
     */
    protected function mapCategories(array $categories): array
    {
        $mapped = [];
        
        foreach ($categories as $category) {
            $slug = $category['slug'];
            $mappedCategory = config('woocommerce.category_mapping')[$slug] ?? $slug;
            $mapped[] = $mappedCategory;
        }
        
        return $mapped;
    }

    /**
     * Map WooCommerce tags
     */
    protected function mapTags(array $tags): array
    {
        return array_column($tags, 'name');
    }

    /**
     * Handle webhook from WooCommerce
     */
    public function handleWebhook(array $data): void
    {
        $action = $data['action'] ?? null;
        $product = $data['product'] ?? null;

        if (!$action || !$product) {
            return;
        }

        switch ($action) {
            case 'created':
            case 'updated':
                $this->syncProduct($product);
                break;
            case 'deleted':
                Course::where('woocommerce_id', $product['id'])->delete();
                break;
        }
    }
}
