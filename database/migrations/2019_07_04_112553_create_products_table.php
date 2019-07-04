<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment("商品名称");
            $table->text('description')->comment('商品描述');
            $table->string('image')->comment("商品缩略图");
            $table->boolean('on_sale')->default(true)->comment("是否上架,默认上架");
            $table->float('rating')->default(5)->commetn("商品评价");
            $table->unsignedBigInteger('sold_count')->default(0)->comment("销量");
            $table->unsignedBigInteger('review_count')->default(0)->comment("评价数量");
            $table->decimal('price',10,2)->comment("SKU最低价格");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
