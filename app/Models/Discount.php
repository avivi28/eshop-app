<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'buy_quantity',
        'percentage',
        'is_active',
        'start_date',
        'end_date',
        'product_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'buy_quantity' => 'integer',
        'percentage' => 'integer',
        'product_id' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
