<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\CartTransformers;
use App\Models\CartItem;
use App\Services\CartService;
use App\Http\Requests\Api\CartRequest;
use App\Models\ProductSku;

class CartController extends Controller
{
    protected $cartService;
    //
    //利用laravel自动解析功能注入CartService类
    public function __construct(CartService $cartSerice)
    {
        $this->cartService = $cartSerice;
    }
    public function  index(CartItem $cart)
    {
        $cartItems = $this->cartService->get();
        //addresses= $this->user()->addresses()->orderBy('last_user_at', 'desc')->get();
        return  $this->response->collection($cartItems, new CartTransformers);
    }
    public function add(CartRequest $request){
        $this->cartService->add($request->sku_id,$request->amount);
        return $this->response->noContent()->setStatusCode(201);
    }
    public function remove(ProductSku $sku){
        $this->cartService->remove($sku->id);
        return $this->response->noContent();
    }
}
