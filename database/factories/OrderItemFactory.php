<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\OrderItem;
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(OrderItem::class, function (Faker $faker) {
    //随机取一条商品信息
    $product=Product::query()->where('on_sale',true)->inRandomOrder()->first();
    //
    $sku=$product->skus()->inRandomOrder()->first();
    return [
        //
        'amount'=>random_int(1,5),
        'price'=>$sku->price,
        'rating'=>null,
        'review'=>null,
        'reviewed_at'=>null,
        'product_id'=>$product->id,
        'product_sku_id'=>$sku->id,
    ];
});
