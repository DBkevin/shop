<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable=[
        'title','description','image','on_sale',
        'rating','sold_count','review_count','price'
    ];
    //与商品SKU 关联(1对多,当前是1)
    public function skus(){
        return $this->hasMany(ProductSku::class);
    }
}
