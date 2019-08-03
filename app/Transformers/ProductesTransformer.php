<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductesTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'Productsku'
    ];
    public function Transform(Product $product){
        return [
            'id'=>(int)$product->id,
            'title'=>$product->title,
            'description'=>$product->description,
            'image'=>$product->image,
            'rating'=>(int)$product->rating,
            'sold_count'=>(int)$product->sold_count,
            'review_count'=>(int)$product->review_count,
            'price'=>(float)$product->price,
        ];
    }
    public function includeProductsku(Product $product){
        $skus=$product->skus;
        return $this->collection($skus,new ProductskuTransformer());

    }
}