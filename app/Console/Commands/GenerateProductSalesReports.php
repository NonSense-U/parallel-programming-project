<?php

namespace App\Console\Commands;

use App\Services\AdminService;
use Illuminate\Console\Command;

class GenerateProductSalesReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:product-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate per-product sales reports and dispatch chunked background jobs.';

    public function __construct(protected AdminService $adminService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->adminService->generateProductSalesReports();

        $this->info('Dispatch complete.');

        return 0;
    }
}
