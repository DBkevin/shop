<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;

class OrdersController extends Controller
{
    // 利用 Laravel 的自动解析功能注入 CartService 类
    /*
    public function store(OrderRequest $request, CartService $cartService)
    {
        $user = $request->user();

        //开启一个数据库事务,
        $order = \DB::transaction(function () use ($user, $request,$cartService) {
            $address = UserAddress::find($request->input('address_id'));
            //更新此地址最后使用时间..
            $address->update(['last_user_at' => Carbon::now()]);
            //创建一个订单
            $order = new Order([
                'address' => [
                    //将地址信息放入订单中,
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $request->input('remark'),
                'total_amount' => 0,
            ]);
            //订单关联到当前用户
            $order->user()->associate($user);
            //写入数据库
            $order->save();

            $totalAmount = 0;
            $items = $request->input('items');
            //遍历用户提交的SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }


            //更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            //将下单的商品从购物车中删除
            $skuIds = collect($items)->pluck('sku_id');
            //$user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
            $cartService->remove($skuIds);
            return $order;
        });
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
    */

    public  function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();
        return view('orders.index', ['orders' => $orders]);
    }


    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function received(Order $order,Request $request){
        //校验权限
        $this->authorize('own',$order);

        //判断订单的发货状态是否是已支付
        if($order->ship_status !==Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update(['ship_status'=>Order::SHIP_STATUS_RECEIVED]);

        //返回原页面
        return $order;
    }

    public function review(Order $order){
        //检验权限
        $this->authorize('own',$order);
        //判断是否已经支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单还未支付,不能评价');
        }

        //使用laod方法加载关联数据,避免N+1的 问题
        return view('orders.review',['order'=>$order->load(['items.productSku','items.product'])]);
    }
    public function sendReview(Order $order,SendReviewRequest $request){
        //校验权限,
        $this->authorize('own',$order);
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单还未支付,不可评价');
        }
        //判断是否评价过了
        if($order->reviewed){
            throw new InvalidRequestException('该订单已评价,不可重复提交');
        }

        $reviews = $request->input('reviews');

        //开启事务,
        \DB::transaction(function () use ($reviews,$order) {
            //遍历用户提交的数据
            foreach ($reviews as $review) {
                # code...
                $orderItem=$order->items()->find($review['id']);
                //保存评分和评价
                $orderItem->update([
                    'rating'=>$review['rating'],
                    'review'=>$review['review'],
                    'reviewed_at'=>Carbon::now(),
                ]);
            }

            //将订单标记未已评价
            $order->update(['reviewed'=>true]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}
