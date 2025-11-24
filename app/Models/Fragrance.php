<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fragrance extends Model
{
    use HasFactory;

    protected $table = 'fragrance';

    protected $fillable = [
        'code',
        'name',
        'gender',
        'description',
        'origin',
        'is_active',
    ];
}
