<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Exceptions\CouponCodeUnavilableException;

class CouponCode extends Model
{
    //定义常量
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';


    public static $typeMap = [
        self::TYPE_FIXED => "固定金额",
        self::TYPE_PERCENT => '比例',
    ];
    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];
    protected $casts = [
        'enabled' => 'boolean',
    ];
    //指定这2个字段是日期
    protected $dates = ['not_before', 'not_after'];
    //返回一个虚假的字段(description)
    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满' . str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str . '优惠' . str_replace('.00', '', $this->value) . '%';
        }
        return $str . '减' . str_replace('.00', '', $this->value);
    }

    public static function findAvailableCode($length = 16)
    {
        do {
            //生成一个指定长度的随机字符串,并转换成大写
            $code = strtoupper(Str::random($length));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    /**
     * 接受一个订单金额,判断对应的优惠券是否合规
     *
     * @param [floot] $orderAmount
     * @return void
     */
    public function checkAvailable($orderAmount = null)
    {
        if (!$this->enabled) {
            throw new CouponCodeUnavilableException('优惠券不存在');
        }

        if ($this->total - $this->used <= 0) {
            throw new CouponCodeUnavilableException('该优惠券已经兑换完');
        }
        if ($this->not_before && $this->not_before->gt(Carbon::now())) {
            throw new CouponCodeUnavilableException('该优惠券现在不可用');
        }
        if ($this->not_after && $this->not_after->lt(Carbon::now())) {
            throw new CouponCodeUnavilableException('该优惠券已经过期');
        }
        if (!is_null($orderAmount) && $orderAmount < $this->min_amount) {
            throw new CouponCodeUnavilableException('该订单金额不满足该优惠券的最近金额');
        }
    }

    /**
     * 计算优惠后的金额
     *
     * @param [int] $orderAmount
     * @return void
     */
    public function getAdjustedPrice($orderAmount)
    {
        //固定金额
        if ($this->type === self::TYPE_FIXED) {
            //为保证系统健壮性,我们需要订单金额最少为0.01元
            return max(0.01, $orderAmount - $this->value);
        }
        return number_format($orderAmount*(100-$this->value)/100,2,'.',"");
    }


    public function changeUsed($increase=true){
        //传入true 代表新增用量,否则就是减少用理
        if($increase){
            return $this->where('id',$this->id)->where('used','<',$this->total)->increment('used');
        }else{
            return $this->decrement('used');
        }
    }
}
