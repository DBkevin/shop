<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSku;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建30个商品
        $products=factory(Product::class,27)->create();
        
        foreach($products as $product){
            //创建三个sku
            $skus=factory(ProductSku::class,3)->create(['product_id'=>$product->id]);
            //找出价格最低的sku,更新
            $product->update(['price'=>$skus->min('price')]);
        }
    }
}
