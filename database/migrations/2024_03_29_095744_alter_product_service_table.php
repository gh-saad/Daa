<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->string('vehicle_id')->nullable();
            $table->string('colour')->nullable();
            $table->string('fuel')->nullable();
            $table->string('mfg_year')->nullable();
            $table->string('vehicle_status')->nullable();
            $table->string('purchased_by')->nullable();
            $table->string('purchased_status')->nullable();
            $table->string('bid_no')->nullable();
            $table->string('bid_date')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('engine_cc')->nullable();
            $table->string('sale_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_services', function (Blueprint $table) {
            $table->dropColumn('vehicle_id');
            $table->dropColumn('colour');
            $table->dropColumn('fuel');
            $table->dropColumn('mfg_year');
            $table->dropColumn('vehicle_status');
            $table->dropColumn('purchased_by');
            $table->dropColumn('purchased_status');
            $table->dropColumn('bid_no');
            $table->dropColumn('bid_date');
            $table->dropColumn('engine_no');
            $table->dropColumn('engine_cc');
            $table->dropColumn('sale_type');
        });
    }
};
