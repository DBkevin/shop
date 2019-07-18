<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

class CouponCodeUnavilableException extends Exception
{
    /**
     * 
     *
     * @param [type] $message
     * @param integer $code
     */
    public function __construct($message ,int $code=403)
    {
        parent::__construct($message,$code);
        
    }
    public function render(Request $request){
        //如果是用户通过Api请求,则返回json格式的错误信息
        if($request->expectsJson()){
            return response()->json(['msg'=>$this->message],$this->code);
        }
        //否则返回上一页并带上错误信息
        return redirect()->back()->withErrors(['coupon_code'=>$this->message]);
    }
}
