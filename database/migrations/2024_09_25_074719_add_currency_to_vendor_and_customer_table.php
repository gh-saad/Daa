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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('currency')->nullable()->after('balance');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->string('currency')->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
