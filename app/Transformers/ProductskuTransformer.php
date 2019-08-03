<?php

namespace App\Transformers;

use App\Models\ProductSku;
use League\Fractal\TransformerAbstract;

class ProductskuTransformer extends TransformerAbstract{

    public function  transform(ProductSku $sku){
        return[
            'title'=>$sku->title,
            'description'=>$sku->description,
            'id'=>(int)$sku->id,
            'price'=>(float)$sku->price,
            'stock'=>(int)$sku->stock,
        ];
    }
}