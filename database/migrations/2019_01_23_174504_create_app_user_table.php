<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');                             //姓名
            $table->string('mobile',24)->unique()->nullable();  //登录手机
            $table->tinyInteger('gender')->nullable();          //性别: 1-男，2-女，0-保密
            $table->tinyInteger('status')->default(1);          //用户状态0-待审核，1已审核
            $table->integer('trip_number')->default(0)->comment('行程数');
            $table->string('last_login_token', 1024)->comment('上次登录token')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_users');
    }
}
