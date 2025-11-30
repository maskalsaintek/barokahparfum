<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $table = 'sales_order';   // penting

    protected $fillable = [
        'order_number',
        'order_date',
        'customer_name',
        'total_before_discount',
        'total_discount',
        'total_tax',
        'total_amount',
        'total_profit',
        'total_profit_percent',
        'total_cost_of_goods',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'total_before_discount' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'total_cost_of_goods' => 'decimal:2',
        'total_profit_percent' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }
}
