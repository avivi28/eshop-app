<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;
use App\Models\Product;

class DiscountProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'product_id'
    ];

    protected $casts = [
        'discount_id' => 'integer',
        'product_id' => 'integer'
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
