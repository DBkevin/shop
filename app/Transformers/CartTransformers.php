<?php

namespace App\Transformers;

use App\Models\CartItem;
use League\Fractal\TransformerAbstract;

class CartTransformers extends TransformerAbstract
{

    /*protected $availableIncludes = [
        'user'
    ];
    */
    
   protected $defaultIncludes = [
        'user'
    ];
   
   
    public function  transform(CartItem $cart)
    {
        return [
            'id' => $cart->id,
            'amount' => (string) $cart->amount,
            'product_sku_id' => $cart->product_sku_id,
            'product_sku' => $cart->productSku,

        ];
    }
    public function includeUser(CartItem $cart)
    {
        $userAddress = $cart->user->addresses;

        return $this->collection($userAddress, new UserAddressesTransformer());
    }
}
