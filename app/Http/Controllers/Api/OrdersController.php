<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;
use App\Transformers\OrdersTransformers;
use App\Http\Requests\Api\OrdersRequest;
use App\Models\UserAddress;
class OrdersController extends Controller
{
    //

    public function index(){
        $user=$this->user()->id;
        $orders=Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $user)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return $this->response->paginator($orders,new OrdersTransformers());
    }

    public function store(OrdersRequest $request,OrderService $orderService)
    {
        $user=$this->user();
        $address=UserAddress::find($request->address_id);
        $coupon=null;

        return $orderService->store($user,$address,$request->remark,$request->items);

    }

}
