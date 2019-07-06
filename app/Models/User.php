<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    //mustVerifyEmail邮箱验证
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * 一对多关联address
     *
     * @return void
     */
    public function addresses(){
        return $this->hasMany(UserAddress::class);
    }
    /**
     * 一对多中间表关联商品表,当前一
     *
     * @return void
     */
    public function favoriteProducts(){
        return $this->belongsToMany(Product::class,'user_favorite_products')->withTimestamps()->orderBy('user_favorite_products.created_at','desc');
    }

    /**
     * 一对多关联cartitems,当前1
     *
     * @return void
     */
    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }
}
