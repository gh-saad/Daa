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
        if (!Schema::hasTable('tax_deductions'))
        {
            Schema::create('tax_deductions', function (Blueprint $table) {
                $table->id();
                $table->integer('employee_id');
                $table->string('title');
                $table->decimal('salary_amount', 8, 2);
                $table->decimal('difference', 8, 2);
                $table->string('tax_deduction_value_type');
                $table->decimal('tax_deduction_value', 8, 2);
                $table->decimal('tax_deduction_calculated', 8, 2);
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
        Schema::dropIfExists('tax_deductions');
    }
};
