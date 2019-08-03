<?php

namespace App\Http\Requests\Api;

use App\Models\ProductSku;
use Illuminate\Validation\Rule;
class OrdersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
           'address_id'=>[
               'required',
               Rule::exists('user_addresses','id')->where('user_id',$this->user()->id),
           ],
           'items'=>['required','array'],
           'items.*.sku_id'=>[//检查items数组下的每一个子数组的sku_id参数
               'required',
               function ($attribute,$value,$fail){
                    if(!$sku=ProductSku::find($value)){
                        return $fail('改商品不存在');
                    }
                    if(!$sku->product->on_sale){
                        return $fail('该商品未上架');
                    }
                    if($sku->strock===0){
                        return $fail("该商品已经售完");
                    }
                    //获取当前索引
                    preg_match('/items\.(\d+)\.sku_id/',$attribute,$m);
                    $index=$m[1];
                    //根据索引找到用户所提交的购买数量
                    $amount=$this->input('items')[$index]['amount'];
                    if($amount>0 && $amount >$sku->stock){
                        return $fail('该商品库存不足');
                    }
               },
           ],
           'items.*.amount'=>['required','integer','min:1'],
        ];
    }

    public function messages(){
        return [
            'address_id.required'=>'请选择下单地址',
            'items.required'=>'请选择要下单的商品',
            'items.array'=>'商品格式错误,请重新下单',
        ];
    }
}
