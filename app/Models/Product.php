<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'price'
    ];

    protected $casts = [
        'price' => 'integer'
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discounts_products');
    }
}
