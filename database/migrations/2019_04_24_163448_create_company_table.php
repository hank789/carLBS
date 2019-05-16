<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name',128)->unique()->comment('公司名称');
            $table->tinyInteger('status')->default(1)->index('company_status')->comment('状态');
            $table->tinyInteger('company_type')->default(1)->index('company_type')->comment('公司类型：1主公司，2供应商');
            $table->timestamps();
        });
        Schema::create('company_rel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->default(1)->index('vendor_company_company_id')->comment('公司id');
            $table->integer('vendor_id')->default(1)->index('vendor_company_vendor_id')->comment('供应商公司id');
            $table->tinyInteger('status')->default(1)->index('vendor_company_status')->comment('状态');
            $table->timestamps();
        });
        Schema::table(config('access.table_names.users'), function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->default(1)->index('users_company_id')->after('uuid')->comment('公司id');
        });
        Schema::table('transport_main', function (Blueprint $table) {
            $table->integer('company_id')->unsigned()->default(1)->index('transport_main_company_id')->after('user_id')->comment('公司id');
            $table->integer('vendor_company_id')->unsigned()->default(1)->index('transport_main_vendor_company_id')->after('user_id')->comment('供应商公司id');
        });
        Schema::table('transport_entity', function (Blueprint $table) {
            $table->integer('last_company_id')->unsigned()->default(1)->index('transport_entity_last_company_id')->after('last_loc_time')->comment('最近服务的公司id');
            $table->integer('last_vendor_company_id')->unsigned()->default(1)->index('transport_entity_last_vendor_company_id')->after('last_loc_time')->comment('最近服务的供应商id');
            $table->integer('last_sub_status')->default(1)->index('transport_entity_last_sub_status')->after('last_loc_time')->comment('最近行程状态');
            $table->string('last_geohash',32)->default('')->index('transport_entity_last_geohash')->after('last_loc_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company');
        Schema::dropIfExists('company_rel');
    }
}
