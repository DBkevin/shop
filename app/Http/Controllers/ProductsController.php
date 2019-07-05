<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{


    public function index(Request $request)
    {
        $builder = Product::query()->where('on_sale', true);
        //判断是否有搜索提交
        if($search=$request->input('search','')){
            $like='%'.$search.'%';
            //模糊搜索标题,详情,m描述等
            $builder->where(function($query) use ($like){
                $query->where('title','like',$like)
                ->orWhere('description','like',$like)
                ->orWhereHas('skus',function($query) use ($like){
                    $query->where('title','like',$like)
                    ->orWhere('description','like',$like);
                });
            });
        }
        //是否有order参数
        if($order=$request->input('order','')){
            //是否以_asc或者_desc结尾
            if(preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                 // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                 if(in_array($m[1],['price','sold_count','rating'])){
                     //根据传入的值来构造排序参数
                     $builder->orderBy($m[1],$m[2]);
                 }
            }
        }
        $products=$builder->paginate(16);

        return view('productes.index', [
            'products' => $products,
            'filters'  =>[
                'search'=>$search,
                'order'=>$order,
            ],
        ]);
    }

    public function show(Product $product,Request $request){
        //是否上架
        if(!$product->on_sale){
            throw new \Exception('商品未上架');
        }
        return view('productes.show',['product'=>$product]);
    }
}
