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
        Schema::create('salary_modification_template', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('allowance')->nullable();
            $table->json('commission')->nullable();
            $table->json('loan')->nullable();
            $table->json('saturation_deduction')->nullable();
            $table->json('tax_deduction')->nullable();
            $table->json('tax_relief')->nullable();
            $table->json('other_payment')->nullable();
            $table->json('overtime')->nullable();
            $table->integer('workspace')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_modification_template');
    }
};
