<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantType extends Model
{
    use HasFactory;

    // karena nama tabel kamu: variant_type (bukan variant_types)
    protected $table = 'variant_type';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];
}
