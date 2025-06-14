<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;
    protected $table = 'order_item';
    protected $fillable = [
        'order_id',
        'product_id',
        'product_price',
        'product_quantity',
       
    ];
    
      public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id' );
    }
      public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id' );
    }
    
}
