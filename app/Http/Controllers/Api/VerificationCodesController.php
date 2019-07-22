<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        if (!app()->environment('production')) {
            $code = '123456';
        } else {
            $phone = $request->phone;
            //生成6位随机码
            $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'content' => "【厦门美莱】您的验证码是{$code}.10分钟有效,如非本人操作,请忽略本短信",
                    'template' => 'SMS_164825837',
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                return $this->responese()->errorInternal($message ?: '短信发送异常');
            }
        }
        $key = 'verificationCode_' . str_random(15);
        $expireAt = now()->addMicros(15);
        //缓存验证码15分钟过期,
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expireAt);

        return  $this->response->array([
            'key' => $key,
            'expired_at' => $expireAt->toDateTimeString()
        ])->setStatusCode(201);
    }
}
