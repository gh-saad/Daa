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
            if (!Schema::hasColumn('product_services', 'purchased_from')) {
                $table->string('purchased_from')->nullable()->after('purchased_by');
            }
            if (!Schema::hasColumn('product_services', 'sold_to')) {
                $table->string('sold_to')->nullable()->after('purchased_status');
            }
            if (!Schema::hasColumn('product_services', 'sold_status')) {
                $table->string('sold_status')->nullable()->after('sold_to');
            }
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
            $table->dropColumn('purchased_from');
            $table->dropColumn('sold_to');
            $table->dropColumn('sold_status');
        });
    }
};
