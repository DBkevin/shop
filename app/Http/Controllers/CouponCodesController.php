<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;
use App\Exceptions\CouponCodeUnavilableException;
use App\Http\Requests;

class CouponCodesController extends Controller
{

    public function show(Request $request,$code)
    {
        //判断优惠券是否存在,
        if (!$recode = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavilableException('优惠券不存在');
        }
        $recode->checkAvailable($request->user());
        return $recode;
    }
    //
}
