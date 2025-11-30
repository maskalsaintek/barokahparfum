<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variant';

    protected $fillable = [
        'fragrance_id',
        'variant_type_id',
        'bottle_size_ml',
        'base_price',
        'cost_ml',
        'mix_ratio',
        'is_active',
    ];

    protected $casts = [
        'bottle_size_ml' => 'float',
        'base_price'     => 'float',
        'cost_ml'     => 'float',
        'is_active'      => 'boolean',
    ];

    public function fragrance()
    {
        return $this->belongsTo(Fragrance::class, 'fragrance_id');
    }

    public function variantType()
    {
        return $this->belongsTo(VariantType::class, 'variant_type_id');
    }
}
