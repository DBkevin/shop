<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;


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
            throw new InvalidRequestException('商品未上架');
        }
        $favored=false;
        if($user=$request->user()){
            $favored=boolval($user->favoriteProducts()->find($product->id));
        }
        return view('productes.show',['product'=>$product,'favored'=>$favored]);
    }


    public function favor(Product $product,Request $request){
        $user=$request->user();
        if($user->favoriteProducts()->find($product->id)){
            return [];
        }
        //attach() 方法的参数可以是模型的 id，也可以是模型对象本身，因此这里还可以写成 attach($product->id)。
        $user->favoriteProducts()->attach($product);
        return [];
    }
    public function disfavor(Product $product,Request $request){
        $user=$request->user();
        $user->favoriteProducts()->detach($product);
        return [];
    }
}
