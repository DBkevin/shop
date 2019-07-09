<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->unique()->commetn('订单号,唯一');
            $table->unsignedBigInteger('user_id')->comment('用户ID,关联用户表,级联删除');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('address')->comment('json 格式存储订单地址');
            $table->decimal('total_amoun',10,2)->comment('订单总金额');
            $table->text('remark')->nullable()->comment('订单备注,允许为空');
            $table->dateTime('paid_at')->nullable()->comment('支付时间,允许为空');
            $table->string('payment_method')->nullable()->comment("支付方式,允许为空");
            $table->string('payment_no')->nullable()->unique()->comment('支付平台订单号,允许为空,唯一');
            $table->string('refund_status')->default(\App\Models\Order::REFUND_STATUS_PENDING)->comment('退款状态,默认未退款');
            $table->string('refund_no')->nullable()->comment("退款单号,允许为空");
            $table->boolean('closed')->default(false)->comment('订单是否关闭,默认无');
            $table->boolean('reviewed')->default(false)->comment('订单是否评论,默认无');
            $table->string('ship_status')->default(\App\Models\Order::SHIP_STATUS_PENDING)->comment('物流状态,默认未发货');
            $table->text('ship_data')->nullable()->comment('物流数据,允许为空');
            $table->text('extra')->nullable()->comment('其他额外数据,允许为空');
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
        Schema::dropIfExists('orders');
    }
}
