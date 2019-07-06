<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    //
    protected $fillable=['amount'];

    public $timestamps=false;

    /**
     * 关联用户表表,1对多,当前多
     *
     * @return void
     */
    public function user(){
        return  $this->belongsTo(User::class);
    }

    /**
     * 关联 SKU表,1对多,当前多
     *
     * @return void
     */
    public function productSku(){
        return $this->belongsTo(ProductSku::class);
    }
}


