<?php

namespace App\Console\Commands;

use App\Services\WooCommerceService;
use Illuminate\Console\Command;

class SyncWooCommerceProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync 
                            {--force : Force sync even if auto-sync is disabled}
                            {--limit=100 : Limit number of products to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from WooCommerce to local database';

    /**
     * Execute the console command.
     */
    public function handle(WooCommerceService $wooCommerceService): int
    {
        if (!config('woocommerce.auto_sync') && !$this->option('force')) {
            $this->warn('Auto-sync is disabled. Use --force to override.');
            return 1;
        }

        $this->info('Starting WooCommerce sync...');

        try {
            $limit = (int) $this->option('limit');
            $results = $wooCommerceService->syncAllProducts();

            $this->info('Sync completed successfully!');
            $this->table(
                ['Action', 'Count'],
                [
                    ['Created', $results['created']],
                    ['Updated', $results['updated']],
                    ['Errors', $results['errors']],
                    ['Skipped', $results['skipped']],
                ]
            );

            if ($results['errors'] > 0) {
                $this->warn("There were {$results['errors']} errors during sync. Check logs for details.");
                return 1;
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }
    }
}