<?php

use Illuminate\Database\Migrations\Migration;
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
        // Modify 'customer_id' to be nullable
        DB::statement('ALTER TABLE revenues MODIFY customer_id INTEGER NULL;');
        // Modify 'user_id' to be nullable
        DB::statement('ALTER TABLE revenues MODIFY user_id INTEGER NULL;');
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
