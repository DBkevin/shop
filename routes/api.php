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
    'middleware' => 'serializer:array'
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
        $api->get('productes/{id}','ProductesController@show')
            ->name('api.productes.show');
        //需要token才能访问的
        $api->group(['middleware' => 'api.auth'], function ($api) {
            //当前登陆用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            $api->patch('user','UsersController@update')
                ->name('api.user.update');
            // 图片资源
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
        });
    });
});

$api->version('v2', function ($api) {
    $api->get('version', function () {
        return response('this is version v2');
    });
});
