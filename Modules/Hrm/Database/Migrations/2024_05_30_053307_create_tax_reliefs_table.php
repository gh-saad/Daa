<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tax_reliefs'))
        {
            Schema::create('tax_reliefs', function (Blueprint $table) {
                $table->id();
                $table->integer('employee_id');
                $table->string('title');
                $table->string('tax_relief_value_type');
                $table->decimal('tax_relief_value', 8, 2);
                $table->integer('workspace')->nullable();
                $table->integer('created_by');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_reliefs');
    }
};
