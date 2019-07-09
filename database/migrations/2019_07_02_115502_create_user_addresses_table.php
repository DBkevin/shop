<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment("关联User，级联删除");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('province')->comment('省');
            $table->string('city')->comment('市');
            $table->string("district")->comment('区/县');
            $table->string('address')->comment("具体地址");
            $table->unsignedInteger('zip')->comment("邮编");
            $table->string('contact_name')->comment("联系人姓名");
            $table->string('contact_phone')->comment("联系人电话");
            $table->dateTime('last_user_at')->nullable()->comment("最后一次使用时间，允许为空");
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
        Schema::dropIfExists('user_addresses');
    }
}
