<?php

namespace App\Transformers;

use App\Models\OrderItem;
use League\Fractal\TransformerAbstract;

class OrderItemsTransformer extends TransformerAbstract
{
    protected $defaultInclodues=[
        'product'
    ];
    public function transform(OrderItem $item)
    {
        return [
            'id'=>$item->id,
            'amount'=>(int)$item->amount,
            'price'=>(float)$item->price,
            'rating'=>$item->rating,
            'review'=>(string)$item->review,
            'product'=>$item->productSku,
            'product_sku'=>$item->productSku,
        ];
    }

    public function includeproduct(OrderItem $item){
        $product=$item->product;
        return $this->item($product,new ProductesTransformer());
    }
    
}
