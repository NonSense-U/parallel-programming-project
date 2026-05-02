<?php

namespace App\Jobs;

use App\Models\ProductSalesReport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessProductSalesReportBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $productIds;

    public string $startDate;

    public string $endDate;

    public function __construct(array $productIds, string $startDate, string $endDate)
    {
        $this->productIds = $productIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function handle(): void
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $productStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereIn('order_items.product_id', $this->productIds)
            ->select(
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price_snapshot) as revenue')
            )
            ->groupBy('order_items.product_id')
            ->get();

        foreach ($productStats as $stat) {
            ProductSalesReport::updateOrCreate(
                [
                    'product_id' => $stat->product_id,
                ],
                [
                    'units_sold' => (int) $stat->units_sold,
                    'revenue' => number_format((float) $stat->revenue, 2, '.', ''),
                ]
            );
        }
    }
}
