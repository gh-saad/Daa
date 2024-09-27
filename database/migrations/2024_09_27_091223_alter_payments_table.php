<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency')->nullable()->after('amount');
            $table->integer('chart_account_id')->nullable()->after('account_id');
        });
        
        // Modify 'vendor_id' to be nullable
        DB::statement('ALTER TABLE payments MODIFY vendor_id INTEGER NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // dont need
    }
};
