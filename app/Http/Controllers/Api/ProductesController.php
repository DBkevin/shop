<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Transformers\ProductesTransformer;
use App\Models\ProductSku;

class ProductesController extends Controller
{
    //
    public function index()
    {
        $productes = Product::where('on_sale', true)->paginate(16);

        return $this->response->item($productes, new ProductesTransformer());
    }
    public function show(Product $product)
    {
        return $this->response->item($product, new ProductesTransformer());
    }
}
