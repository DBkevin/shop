<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
      public function register(){
        //往服务容器中注入一个名为alipay的单例对象
        $this->app->singleton('alipay',function(){
            $config=config('pay.alipay');
           // $config['notify_url'] = route('payment.alipay.notify');
            //$config['notify_url']='http://requestbin.fullcontact.com/1bbqtw81';
            $config['notify_url']=ngrok_url('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');
            //判断是线上还是线下
            if(app()->environment()!=='production'){
                $config['mode']='dev';
                $config['log']['level']=Logger::DEBUG; 
            }else{
                $config['log']['level']=Logger::WARNING;
            }

            //调用yonsongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });
        $this->app->singleton('wechat_pay',function(){
            $config=config('pay.wechat');
            //$config['notify_url']='http://requestbin.fullcontact.com/1bbqtw81';
            $config['notify_url']=ngrok_url('payment.wechat.notify');
            if(app()->environment()!=='production'){
                $config['log']['level']=Logger::DEBUG;
            }else{
                $config['log']['level']=Logger::WARNING;
            }
            //调用Yonsongda\Pay来创建一个微信支付队形
            return pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
