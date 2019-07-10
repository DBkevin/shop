<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    //利用laravel自动解析功能注入CartService类
    public function __construct(CartService $cartSerice)
    {
        $this->cartService = $cartSerice;
    }
    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();

        $addresses = $request->user()->addresses()->orderBy('last_user_at', 'desc')->get();
        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));
        return [];
    }
    public function remove(ProductSku $sku)
    {
        $this->cartService->remove($sku->id);
        return [];
    }

    /*
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
        $addresses=$request->user()->addresses()->orderBy('last_user_at','desc')->get();
        return view('cart.index', ['cartItems' => $cartItems,'addresses'=>$addresses]);
    }


    public function remove(ProductSku $sku,Request $request){
        $request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();
        return [];
    }
    */
}
