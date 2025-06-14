<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
      protected $table = 'orders';
    protected $fillable = [
        'firstname',
        'surname',
        'email',
        'phone',
        'street',
        'district',
        'city',
        'state',
        'total',
        'reference',
        'user_type'
    ];
    protected $with = ['orderItems'];
     public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}
