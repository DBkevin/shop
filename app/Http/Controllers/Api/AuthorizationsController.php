<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use Auth;

class AuthorizationsController extends Controller
{
    //
    public function sociaStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return  $this->response->errorBadRequest();
        }
        $driver = \Socialite::driver($type);
        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误,未活动用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('uniond') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;
        }

        $token = \Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token);
    }

    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username : $credentials['phone'] = $username;

        $credentials['password'] = $request->password;
        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或者密码错误');
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }

    public  function update()
    {
        $token = \Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destory()
    {
        \Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;
        //根据code获取微信的openid和session_keyi以及unionid
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);
        //如果报错,说明code过期,返回401错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code不正确');
        }
        if (isset($data['unionid'])) {
            $user = User::where('weixin_unionid', $data['unionid'])->first();
        } else {
            $user = User::where('weapp_openid', $data['openid'])->first();
        }

        $attributes['weixin_session_key'] = $data['session_key'];
        //未找到对应用户需要提交用户名密码进行绑定
        if (!$user) {
            // 如果未提交用户名密码，403 错误提示
            if (!$request->username) {
                return $this->response->errorForbidden('用户不存在');
            }
            $username = $request->username;
            // 用户名可以是邮箱或电话
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username : $credentials['phone'] = $username;
            $credentials['password'] = $request->password;
            // 验证用户名和密码是否正确
            if (!Auth::guard('api')->once($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }
            // 获取对应的用户
            $user = Auth::guard('api')->getUser();
            isset($data['unionid']) ? $attributes['weixin_unionid'] = $data['unionid'] : $attributes['weapp_openid'] = $data['openid'];
        }
        //更新用户数据
        $user->update($attributes);
        // 为对应用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }
}
