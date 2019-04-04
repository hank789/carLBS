<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendorPeople extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_main', function (Blueprint $table) {
            $table->string('transport_contact_vendor_people')->nullable()->after('transport_contact_phone')->comment('本次行程供应商联系人');
            $table->string('transport_contact_vendor_phone',32)->nullable()->after('transport_contact_phone')->comment('本次行程供应商联系电话');
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
