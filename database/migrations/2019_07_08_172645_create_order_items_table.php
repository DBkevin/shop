<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单ID,关联orders表,级联删除');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->comment('商品ID,关联products表,级联删除');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('product_sku_id')->comment('商品SKUid,关联product_skus表,级联删除');
            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
            $table->unsignedBigInteger('amount')->comment("商品数量");
            $table->decimal('price',10,2)->comment('单价');
            $table->unsignedBigInteger('rating')->nullable()->comment("用户评分,允许为空");
            $table->text("review")->nullable()->comment('用户打分,允许为空');
            $table->timestamp('reviewed_at')->nullable()->comment("评价时间");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
