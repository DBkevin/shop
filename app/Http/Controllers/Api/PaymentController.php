<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;

class PaymentController extends Controller
{
    //
    
    //
    public function payByAlipay(Order $order, Request $request)
    {
        //判断订单是否输于当前用户
        $this->authorize('own', $order);
        //订单已支付或者关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确',500);
        }
        //调用支付宝的网页支付
        return app('alipay')->wap([
            'out_trade_no' => $order->no, //订单编号,需保证在商户端不重复
            'total_amount' => $order->total_amount, //订单金额,单位元,支持小数点后2位
            'subject' => '支付Laravel Shop的订单' . $order->items[0]->product->title, //订单标题
        ]);
    }
    
}
