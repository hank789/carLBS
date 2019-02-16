<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTravelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //主行程信息
        Schema::create('transport_main', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('transport_main_user_id')->comment('本次行程的录入人员');
            $table->string('transport_number',36)->unique()->comment('行程编码');
            $table->tinyInteger('transport_status')->default(0)->comment('状态');
            $table->string('transport_start_place')->nullable()->comment('行程出发地');
            $table->string('transport_end_place')->nullable()->comment('行程目的地');
            $table->string('transport_contact_people')->nullable()->comment('本次行程的联系人');
            $table->string('transport_contact_phone',32)->nullable()->comment('本次行程的联系电话');
            $table->dateTime('transport_start_time')->nullable()->comment('行程出发时间');
            $table->json('transport_goods')->nullable()->comment('货物信息');
            $table->timestamps();
            $table->softDeletes();
        });
        //司机行程信息
        Schema::create('transport_sub', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('transport_main_id')->unsigned()->index('transport_sub_transport_main_id');
            $table->integer('api_user_id')->unsigned()->index('transport_sub_api_user_id')->comment('司机id');
            $table->tinyInteger('transport_status')->default(0)->comment('状态');
            $table->string('car_number',32)->index('transport_sub_car_number')->nullable()->comment('车牌号');
            $table->dateTime('transport_start_time')->nullable()->comment('行程出发时间');
            $table->string('transport_start_place')->nullable()->comment('行程出发地');
            $table->string('transport_end_place')->nullable()->comment('行程目的地');
            $table->json('transport_goods')->nullable()->comment('货物信息');
            $table->timestamps();
        });
        //司机行程lbs记录
        Schema::create('transport_lbs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_user_id')->unsigned()->index('transport_lbs_api_user_id')->comment('司机id');
            $table->integer('transport_main_id')->unsigned()->index('transport_lbs_transport_main_id');
            $table->integer('transport_sub_id')->unsigned()->index('transport_lbs_transport_sub_id');
            $table->json('address_detail')->comment('详细地址信息');
            $table->timestamps();
        });
        //司机行程事件记录
        Schema::create('transport_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_user_id')->unsigned()->index('transport_events_api_user_id')->comment('司机id');
            $table->integer('transport_main_id')->unsigned()->index('transport_events_transport_main_id');
            $table->integer('transport_sub_id')->unsigned()->index('transport_events_transport_sub_id');
            $table->tinyInteger('event_type')->default(1)->comment('事件类型');
            $table->string('geohash',32)->index('transport_events_geohash');
            $table->json('event_detail')->nullable()->comment('事件描述');
            $table->timestamps();
        });
        //司机卸货记录
        Schema::create('transport_xiehuo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_user_id')->unsigned()->index('transport_xiehuo_api_user_id')->comment('司机id');
            $table->integer('transport_main_id')->unsigned()->index('transport_xiehuo_transport_main_id');
            $table->integer('transport_sub_id')->unsigned()->index('transport_xiehuo_transport_sub_id');
            $table->tinyInteger('xiehuo_type')->default(1)->comment('卸货类型:1目的地卸货，2中途卸货');
            $table->string('geohash',32)->index('transport_xiehuo_geohash');
            $table->json('transport_goods')->nullable()->comment('货物信息');
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
        Schema::dropIfExists('transport_main');
        Schema::dropIfExists('transport_sub');
        Schema::dropIfExists('transport_lbs');
        Schema::dropIfExists('transport_events');
        Schema::dropIfExists('transport_xiehuo');
    }
}
