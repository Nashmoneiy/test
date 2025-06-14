<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
     protected $table = 'product';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'brand',
        'meta_title',
        'status',
        'selling_price',
        'original_price',
        'trending',
        'image',
        'quantity',
        'category_id'
    ];
    protected $with = ['category'];
    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
