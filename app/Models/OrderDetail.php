<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Order;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'qty',
        'unit_price',
        'order_id',
        'product_id',
        'discount_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'discount_id' => 'integer'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
