<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array','bindings']
], function ($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        //游客访问的接口
        $api->get('version', function () {
            return response('this is version v1');
        });
        //短信验证码 VerificationCodesController
        $api->post('verificationCondes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        //用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        //图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
        //三方登陆
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@sociaStore')
            ->name('api.socials.authorizations.store');
        //登陆
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        //更新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizatioins.update');
        $api->delete('authorizations/current', 'AuthorizationsController@destory')
            ->name('api.authorizations.destory');
        $api->get('productes','ProductesController@index')
            ->name('api.productes');
        $api->get('productes/{product}','ProductesController@show')
            ->name('api.productes.show');
        //需要token才能访问的
        $api->group(['middleware' => 'api.auth'], function ($api) {
            //当前登陆用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            //修改个人信息
            $api->patch('user','UsersController@update')
                ->name('api.user.update');
            // 图片资源
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
            //当前登陆用户收货地址
            $api->get('user/addresses','UserAddressesController@index')
                ->name('api.useraddress');
            $api->post('user/addresses','UserAddressesController@store')
                ->name('api.useraddresses.store');
            $api->put('user/address/{UserAddress}','UserAddressesController@update')
                ->name('api.useraddress.update');
            $api->delete('user/address/{UserAddress}','UserAddressesController@destroy')
                ->name('api.useraddress.destroy');
            //购物车
            $api->get('user/cart','CartController@index')
                ->name('api.cart.index');
            $api->post('user/cart','CartController@add')
                ->name('api.cart.add');
            $api->delete('user/cart/{sku}','CartController@remove')
                ->name("api.cart.remove");
            //订单
            $api->get('user/orders','OrdersController@index')
                ->name('api.order.index');
            $api->post('user/orders','OrdersController@store')
                ->name('api.order.store');
            $api->get('user/orders/{order}','OrdersController@show')
                ->name('api.order.show');
            //支付
            $api->get('payment/{order}/alipay','PaymentController@PayByAlipay')
                ->name('api.payment.alipay');
        });
    });
});

$api->version('v2', function ($api) {
    $api->get('version', function () {
        return response('this is version v2');
    });
});
