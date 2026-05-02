<?php

namespace App\Services;

use App\Jobs\ProcessProductSalesReportBatch;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function generateProductSalesReports(int $chunkSize = 100): bool
    {
        $start = now()->subDay()->startOfDay();
        $end = now()->endOfDay();

        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('order_items.product_id')
            ->groupBy('order_items.product_id');

        $query
            ->reorder()
            ->orderBy('order_items.product_id')
            ->chunk($chunkSize, function ($rows) use ($start, $end) {
                $ids = collect($rows)->pluck('product_id')->toArray();
                ProcessProductSalesReportBatch::dispatch($ids, $start->toDateString(), $end->toDateString());
            });

        return true;
    }
}
