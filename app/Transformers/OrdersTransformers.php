<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;
use App\Models\Product;

class OrdersTransformers  extends TransformerAbstract
{
    protected $defaultIncludes=[
        'orderitems',
    ];
    public function transform(Order $order){
        return [
            'id'=>$order->id,
            'no'=>(string)$order->no,
            'total_amount'=>(float)$order->total_amount,
            'remark'=>$order->remark,
            'paid_at'=>$order->paid_at ? $order->paid_at->toDateTimeString() : $order->paid_at,
            'created_at'=>$order->created_at->toDateTimeString(),
            'product_title'=>$order->items('product'),
        ];
    }

    public function includeorderitems(Order $order){
        $items=$order->items;
        return $this->collection($items,new OrderItemsTransformer());

    }
  
}