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
            $table->decimal('clearance_charges',10, 4)->nullable()->after('purchased_from');
            $table->decimal('repairing_charges',10, 4)->nullable()->after('clearance_charges');
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
            $table->dropColumn('clearance_charges');
            $table->dropColumn('repairing_charges');
        });
    }
};
