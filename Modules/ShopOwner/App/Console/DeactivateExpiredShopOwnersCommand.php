<?php

namespace Modules\ShopOwner\App\Console;

use Illuminate\Console\Command;
use Modules\ShopOwner\App\Jobs\DeactivateExpiredShopOwnersJob;

class DeactivateExpiredShopOwnersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop-owners:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate shop owners that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching job to deactivate expired shop owners...');
        DeactivateExpiredShopOwnersJob::dispatch()->onConnection('database');
        $this->info('Job dispatched successfully!');
        return 0;
    }
}