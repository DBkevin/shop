<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //允许写入字段
    protected $fillable=[
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];
    protected $dates=['last_used_at'];

    public function user(){
        //一堆多，user表1，userAddress 多
        return $this->belongsTo(User::class);
    }
    /**
     * fullAddress访问器
     * 访问FullAddress方法返回拼接后的地址
     * @return void
     */
    public function getFullAddressAttribute(){
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }

}
