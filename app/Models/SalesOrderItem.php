<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $table = 'sales_order_item';

    public $timestamps = false;

    protected $fillable = [
        'sales_order_id',
        'product_variant_id',
        'quantity',
        'uom',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'profit_amount',
        'profit_percent',
        'cost_of_goods',
        'line_total',
        'is_free'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'profit_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'profit_percent' => 'decimal:2',
        'cost_of_goods' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
