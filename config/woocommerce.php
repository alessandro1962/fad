<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WooCommerce API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WooCommerce REST API integration
    |
    */

    'api_url' => env('WOOCOMMERCE_API_URL', 'https://sicurezzadigitale.shop/wp-json/wc/v3'),
    'consumer_key' => env('WOOCOMMERCE_CONSUMER_KEY'),
    'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Sync Settings
    |--------------------------------------------------------------------------
    */
    
    'sync_interval' => env('WOOCOMMERCE_SYNC_INTERVAL', 30), // minutes
    'auto_sync' => env('WOOCOMMERCE_AUTO_SYNC', true),
    'webhook_secret' => env('WOOCOMMERCE_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Product Mapping
    |--------------------------------------------------------------------------
    */
    
    'category_mapping' => [
        'corsi-online' => 'online_course',
        'formazione' => 'training',
        'certificazioni' => 'certification',
    ],
    
    'status_mapping' => [
        'publish' => 'active',
        'draft' => 'draft',
        'private' => 'private',
    ],
];
