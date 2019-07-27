<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductesTransformer extends TransformerAbstract
{
    public function Transform(Product $product){
        return [
            'id'=>$product->id,
            'title'=>$product->title,
            'description'=>$product->description,
            'image'=>$product->image,
            'rating'=>(int)$product->rating,
            'sold_count'=>(int)$product->sold_count,
            'review_count'=>(int)$product->review_count,
            'price'=>(float)$product->price,
            'skus'=>$product->skus,
        ];
    }
}