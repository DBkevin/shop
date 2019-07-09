<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable=['amoutn','price','rating','review','reviewed_at'];
    protected $dates=['reviewed_at'];
    public $timestamps=false;
    //与商品表(product的一对1关联,
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    //与商品SKU表1对1关联
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
    /**
     * 与订单总表(order)的一对1关联
     *
     * @return void
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
