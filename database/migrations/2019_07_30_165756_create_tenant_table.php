<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant', function (Blueprint $table) {
            $table->increments('id');
            $table->string('request_id');
            $table->integer('user_id')->unsigned()->index('tenant_user_id');
            $table->string('app_type',10);
            $table->string('app_id');
            $table->string('tenant_id');
            $table->tinyInteger('source')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->json('detail')->nullable()->comment('详细信息');
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
        Schema::dropIfExists('tenant');
    }
}
