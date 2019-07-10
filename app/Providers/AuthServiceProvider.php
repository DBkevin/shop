<?php

namespace App\Providers;

use Monolog\Logger;
use Yansongda\Pay\Pay;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
//use App\Models\UserAddress;
//use App\Policies\UserAddressPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
       // 5.7用法 UserAddress::class=>UserAddressPolicy::class,
    ];
    public function register(){
        //往服务容器中注入一个名为alipay的单例对象
        $this->app->singleton('alipay',function(){
            $config=config('pay.alipay');
            //判断是线上还是线下
            if(app()->environment()!=='production'){
                $config['model']='dev';
                $config['log']['level']=Logger::DEBUG; 
            }else{
                $config['log']['level']=Logger::WARNING;
            }

            //调用yonsongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });
        $this->app->singleton('wechat_pay',function(){
            $config=config('pay.wechat');
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
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // 使用 Gate::guessPolicyNamesUsing 方法来自定义策略文件的寻找逻辑
        Gate::guessPolicyNamesUsing(function ($class) {
            // class_basename 是 Laravel 提供的一个辅助函数，可以获取类的简短名称
            // 例如传入 \App\Models\User 会返回 User
            return '\\App\\Policies\\' . class_basename($class) . 'Policy';
        });

        //
    }
}
