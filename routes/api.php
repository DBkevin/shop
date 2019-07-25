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
    'namespace' => 'App\Http\Controllers\Api'
], function ($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
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
        $api->post('captchas','CaptchasController@store')
            ->name('api.captchas.store');
        //三方登陆
        $api->post('socials/{social_type}/authorizations','AuthorizationsController@sociaStore')
            ->name('api.socials.authorizations.store');
        //登陆
        $api->post('authorizations','AuthorizationsController@store')
            ->name('api.authorizations.store');
        //更新token
        $api->put('authorizations/current','AuthorizationsController@update')
            ->name('api.authorizatioins.update');
        $api->delete('authorizations/current','AuthorizationsController@destory')
            ->name('api.authorizations.destory');
    });
});

$api->version('v2', function ($api) {
    $api->get('version', function () {
        return response('this is version v2');
    });
});
