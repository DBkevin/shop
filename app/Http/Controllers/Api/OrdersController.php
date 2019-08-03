<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;
use App\Transformers\OrdersTransformers;
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
}
