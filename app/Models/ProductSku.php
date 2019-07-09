<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  App\Exceptions\InternalException;


class ProductSku extends Model
{
    //
    protected $fillable=[
        'title','description','price','stock'
    ];

    //与商品1对多关联 (当前多)
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function decreaseStock($amount){
        if($amount<0){
            throw new InternalException('减库存不可小于0');
        }
        //最终执行 update product_skus set stock = stock - $amount where id = $id and stock >= $amount
        return $this->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);
    }

    public function addStock($amount){
        if($amount<0){
            throw new InternalException('加库存不能小于0');
        }
        $this->increment('stock',$amount);
    }

}
