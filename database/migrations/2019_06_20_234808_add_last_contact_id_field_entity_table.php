<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastContactIdFieldEntityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_entity', function (Blueprint $table) {
            $table->integer('last_contact_id')->nullable()->default(0)->index('transport_entity_last_contact_id')->after('last_vendor_company_id')->comment('最近的收货人id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
