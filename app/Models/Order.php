<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_price',
        'total_discount',
        'user_id',
    ];

    protected $casts = [
        'discount_id' => 'integer',
        'product_id' => 'integer',
        'user_id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
