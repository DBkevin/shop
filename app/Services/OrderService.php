<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavilableException;

class OrderService{
    public function store(User $user,UserAddress $address,$remark,$items,CouponCode $coupon=null){

        //如果传入了优惠券
        if($coupon){
            //此时还没有计算金额,先查看是否可用
            $coupon->checkAvailable(); 
        }

        //开启数据库事务
        $order=\DB::transaction(function () use ( $user,$address,$remark,$items,$coupon) {
            //更新地址最后使用时间
            $address->update(['lsts_user_at'=>Carbon::now()]);
            //创建一个订单,
            $order=new Order([
                'address'=>[
                    'address'=>$address->full_address,
                    'zip'=>$address->zip,
                    'contact_name'=>$address->contact_name,
                    'contact_phone'=>$address->contact_phone,
                ],
                'remark'=>$remark,
                'total_amount'=>0,
            ]);
            //订单关联到当前用户
            $order->user()->associate($user);
            //写入数据库
            $order->save();


            $totalAmount=0;
            //遍历用户提交的SKU
            foreach($items as $data){
                $sku=ProductSku::find($data['sku_id']);
                //创建一个OrderItem 并直接与当前订单关联
                $item=$order->items()->make([
                    'amount'=>$data['amount'],
                    'price'=>$sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount+=$sku->price *$data['amount'];
                if($sku->decreaseStock($data['amount'])<=0){
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            if($coupon){
                //总金额计算出来了,检查是否符合优惠券使用规则
                $coupon->checkAvailable($totalAmount);
                //吧订单金额修改为优惠后的金额
                $totalAmount=$coupon->getAdjustedPrice($totalAmount);
                //将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                //增加优惠券的使用量,需判断返回值
                if($coupon->changeUsed()<=0){
                    throw new CouponCodeUnavilableException('该优惠券已经被兑换完');
                }
            }
            //更新订单金额
            $order->update(['total_amount'=>$totalAmount]);
           
            //将下单的商品从购物车中移除
            $skuIds=collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

         // 这里我们直接使用 dispatch 函数
         dispatch(new CloseOrder($order,config('app.order_ttl')));
         return $order;
    }
}