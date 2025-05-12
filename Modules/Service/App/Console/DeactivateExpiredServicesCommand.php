<?php

namespace Modules\Service\App\Console;

use Illuminate\Console\Command;
use Modules\Service\App\Jobs\DeactivateExpiredServicesJob;

class DeactivateExpiredServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate services that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching job to deactivate expired services...');
        DeactivateExpiredServicesJob::dispatch()->onConnection('database');
        $this->info('Job dispatched successfully!');
        return 0;
    }
}