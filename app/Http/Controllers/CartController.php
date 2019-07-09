<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductSku;

class CartController extends Controller
{
    //
    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        //从数据库中查询是否已经在购物车里面了
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            //如果存在,直接叠加数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return [];
    }

    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();

        return view('cart.index', ['cartItems' => $cartItems]);
    }


    public function remove(ProductSku $sku,Request $request){
        $request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();
        return [];
    }

}