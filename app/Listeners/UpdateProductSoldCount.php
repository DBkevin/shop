<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;
use App\Models\OrderItem;

//implements ShouldQueue 是异步执行
class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     *   Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        //从事件对象中取出对应的订单
        $order=$event->getOrder();
        //预加载商品数据
        $order->load('items.product');

        //循环遍历订单的商品
        foreach($order->items as $item){
            $product=$item->product;
            //计算对应的商品的销量
            $soldCount=OrderItem::query()
                ->where('product_id',$product->id)
                ->whereHas('order',function($query){
                    $query->whereNotNUll('paid_at');//关联的订单状态是已经支付
                })->sum('amount');
            //更新商品数量
            $product->update([
                'sold_count'=>$soldCount,
            ]);
        }

    }
}
