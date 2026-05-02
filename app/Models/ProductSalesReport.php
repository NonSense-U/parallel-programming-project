<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSalesReport extends Model
{
    protected $table = 'product_sales_reports';

    protected $fillable = [
        'product_id',
        'report_date',
        'units_sold',
        'revenue',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
