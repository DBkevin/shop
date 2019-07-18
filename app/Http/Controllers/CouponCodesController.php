<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;
class CouponCodesController extends Controller
{

    public function show($code){
        //判断优惠券是否存在,
        if(!$recode=CouponCode::where('code',$code)->first()){
            abort(404);
        }
        //如果优惠券没有启用,则等于不存在
        if(!$recode->enabled){
            abort(404);
        }
        if($recode->total-$recode->used<=0){
            return response()->json(['msg'=>'该优惠券已经被兑完'],403);
        }
        if($recode->not_before && $recode->not_before->gt(Carbon::now())){
            return  response()->json(['msg'=>'该优惠券现在还不能使用'],403);
        }
        if($recode->not_after &&  $recode->not_after->lt(Carbon::now())){
            return response()->json(['msg'=>'该优惠券已经过期'],403);
        }

        return $recode;
    }
    //
}
