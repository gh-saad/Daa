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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('registration_no');
            $table->string('company_name', 255);
            $table->string('logo', 255)->nullable();
            $table->unsignedBigInteger('relational_manager')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('company_whatsapp', 255);
            $table->string('GM_whatsapp', 255)->nullable();
            $table->string('marketing_director_no', 255)->nullable();
            $table->string('trade_license', 255);
            $table->date('trno_expiry');
            $table->string('agency_license_number', 255)->nullable();
            $table->string('trno_issue_place', 255)->nullable();
            $table->string('po_box', 255)->nullable();
            $table->string('trn_certificate', 255);
            $table->string('rera_certificate', 255)->nullable();
            $table->string('passport', 255);
            $table->string('emirates_id', 255);
            $table->string('rara_card', 255);
			$table->string('brokage_agreement')->nullable();
			$table->boolean('is_agreement_signed')->default(false);
            $table->string('bank_name', 255)->nullable();
            $table->string('ac_name', 255)->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->string('branch_address', 255)->nullable();
            $table->string('currency', 255)->nullable();
            $table->string('swift_code', 255)->nullable();
            $table->string('iban', 255)->nullable();
            $table->foreignId('created_by')->constrained('users')->after('iban');
            $table->enum('status', ['pending', 'Approved', 'Rejected']);
            $table->tinyInteger('is_submitted')->default(0);
            $table->string('reason', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('relational_manager');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
};
