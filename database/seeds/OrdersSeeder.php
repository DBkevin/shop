<?php

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取Faker实例
        $faker=app(Faker\Generator::class);
        //创建100笔订单
        $orders=factory(Order::class,100)->create();
        //被购买的商品,用于后面更新商品销量和评分
        $products=collect([]);
        foreach($orders as $order){
            //没笔订单随机1-3个商品
            $items=factory(OrderItem::class,random_int(1,3))->create([
                'order_id'=>$order->id,
                'rating'=>$order->reviewed?random_int(1,5):null,
                'review'=>$order->reviewd?$faker->sentence:null,
                'reviewed_at'=>$order->reviewed?$faker->dateTimeBetween($order->paid_at):null,
            ]);
            //计算总价
            $total=$items->sum(function (OrderItem $item){
                return $item->price* $item->amount;
            });
            //如果有优惠券,则计算优惠后的价格
            if($order->couponCode){
                $total=$order->couponCode->getAdjustPrice($total);
            }

            //更新订单总价
            $order->update([
                'total_amount'=>$total,
            ]);
            //将这笔订单的商品合并到商品集合中
            $products=$products->merge($items->pluck('product'));
        }
        //根据商品ID,过滤掉重复的商品
        $products->unique('id')->each(function(Prodcut $prodcut){
            //查出该商品的销量,评分,评价数
            $result=OrderItem::query()
                ->where('product_id',$prodcut->id)
                ->whereHas('order',function($query){
                    $query->whereNotNUll('paid_at');    
                })
                ->first([
                    \DB::raw('count(*) as review_count'),
                    \DB::raw('avg(rating) as rating'),
                    \DB::raw('sum(amount) as sold_count'),
                ]);
            $product->update([
                'rating'=>$result->rating?:5,
                'review_count'=>$result->review_count,
                'sold_count'=>$result->sold_count,
            ]);
        });
    }
}
