<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    //
    public function payByAlipay(Order $order, Request $request)
    {
        //判断订单是否输于当前用户
        $this->authorize('own', $order);
        //订单已支付或者关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        //调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, //订单编号,需保证在商户端不重复
            'total_amount' => $order->total_amount, //订单金额,单位元,支持小数点后2位
            'subject' => '支付Laravel Shop的订单' . $order->items[0]->product->title, //订单标题
        ]);
    }
    //前端回调
    public function alipayReturn()
    {
        try {
            //
            app('alipay')->verify();
        } catch (\Exception $e) {
            //throw $th;
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    //服务端回调
    public function alipayNotify()
    {
        //校验输入参数
        $data = app('alipay')->verify();
        //如果订单状态不是成功或者结束,则不走后续的逻辑
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return  app('alipay')->success();
        }
        //$data->out_trade_no拿到流水号,并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        //如果这笔订单的状态是已支付,
        if ($order->paid_at) {
            //返回数据给支付宝
            return  app('alipay')->success();
        }
        $order->update([
            'paid_at' => Carbon::now(), //支付时间
            'payment_method' => 'alipay', //支付方式
            'payment_no' => $data->trade_no, //支付宝订单号
        ]);
        
        $this->afterPaid($order);

        return app('alipay')->success();
    }


    //微信支付
    public function payByWechat(Order $order, Request $request)
    {
        //权限校验
        $this->authorize('own', $order);
        //校验订单状态
        if ($order->paid_at || $order->closed) {
            throw new  InvalidRequestException('订单状态不正确');
        }
        //scan 方法为拉起微信扫码付款 
       $wechatOrder=app('wechat_pay')->scan([
            'out_trade_no' => $order->no, //商户订单流水号,与支付宝订单our_trade_no一致
            'total_fee' => $order->total_amount * 100, //单位分
            'body' => '支付shop的订单:' . $order->items[0]->product->title,
        ]);
        $qrCode=new QrCode($wechatOrder->code_url);
        //将生成的二维码图片以数据字符串形式输出,并带上响应的响应类型
        return response($qrCode->writeString(),200,['Content-Type'=>$qrCode->getContentType()]);
    }

    public function wechatNotify()
    {
        //校验回调参数是否正确
        $data = app('wechat_pay')->verify();
        $order = Order::where('no', $data->out_trade_no)->first();

        //订单不存在告知微信支付
        if (!$order) {
            return 'fail';
        }
        //订单支付
        if ($order->paid_at) {
            //告知微信支付此订单已经支付过了
            return app('wechat_pay')->success();
        }

        //将订单标记为支付
        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'wechat',
            'payment_no' => $data->transaction_id,
        ]);
        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    protected function afterPaid(Order $order){
        event(new OrderPaid($order));
    }
    /**
     * 微信退款通知
     *
     * @param Request $request
     * @return void
     */
    public function wechatRefundNotify(Request $request){
        //给微信的失败响应
        $failXml='<xml><return_code><![CDATA[FAIL]]></refund_code><return_msg><!CDATA[FAIL]]></return_msg></xml>';
        $data=app('wechat_pay')->verify(null,true);

        //没有找到对应的订单 ,原则上不能发生,保证代码健壮性
        if(!$order=Order::where('no',$data['out_trade_no'])->first()){
            return $failXml;
        }

        if($data['refund_status'] ==='SUCCESS'){
            //退款成功,将订单退款状态修改为退款成功
            $order->update([
                'refund_status'=>Order::REFUND_STATUS_SUCCESS,
            ]);
        }else{
            //退款失败,将具体的状态写extra 字段,并修改状体为失败
            $extra=$order->extra;
            $extra['refund_failed_code']=$data['refund_status'];
            $order->update([
                'refund_status'=>Order::REFUND_STATUS_FAILED,
                'extra'=>$extra
            ]);
        }

        return app('wechat_pay')->success();
    }
}
