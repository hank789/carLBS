<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppVersionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_version', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('app_version')->unique()->commnet('版本号');
            $table->string('package_url')->nullable()->commnet('版本下载地址');
            $table->tinyInteger('is_ios_force')->default(0)->comment('是否ios强更:0非强更,1强更');
            $table->tinyInteger('is_android_force')->default(0)->comment('是否android强更:0非强更,1强更');
            $table->string('update_msg')->nullable()->comment('更新内容');
            $table->tinyInteger('status')->default(0)->comment('状态:0未生效,1已生效');
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
        Schema::dropIfExists('app_version');
    }
}
