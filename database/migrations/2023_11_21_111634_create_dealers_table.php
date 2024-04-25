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
            $table->id(); // id of this dealer
            $table->unsignedBigInteger('user_id'); // foreign key referencing the user associated with the dealer
            $table->string('company_name', 255); // name of company
            $table->string('logo', 255)->nullable(); // path to file (example: '/uploads/company_logos/this_company_logo.png')
            $table->unsignedBigInteger('relational_manager')->nullable(); // foreign key referencing the user who is a relational manager for this dealer
            $table->string('website', 255)->nullable(); // website url of the company
            $table->string('company_whatsapp', 255)->nullable(); // whatsapp number for communication with the company
            $table->string('GM_whatsapp', 255)->nullable(); // general managers whatsapp number
            $table->string('marketing_director_no', 255)->nullable();  // marketing director phone number
            $table->string('dealer_document', 255)->nullable(); // path to file (example: '/uploads/company_documents/dealer_document/this_dealer_document.png')
            $table->string('passport_copy', 255)->nullable(); // path to file (example: '/uploads/company_documents/passport_copy/this_passport_copy.png')
            $table->string('trade_license', 255)->nullable(); // path to file (example: '/uploads/company_documents/trade_license/this_trade_license.png')
            $table->string('emirates_document', 255)->nullable(); // path to file (example: '/uploads/company_documents/emirates_document/this_emirates_document.png')
            $table->string('tax_document', 255)->nullable(); // path to file (example: '/uploads/company_documents/tax_document/this_tax_document.png')
            $table->string('security_cheque_copy', 255)->nullable(); // path to file (example: '/uploads/company_documents/security_cheque_copy/this_security_cheque_copy.png')
            $table->string('po_box', 255)->nullable();  // post office box if any
            $table->boolean('is_agreement_signed')->default(false);  // is agreement signed or not?
            $table->string('bank_name', 255)->nullable(); // bank name
            $table->string('ac_name', 255)->nullable(); // bank account holder name
            $table->string('branch_name', 255)->nullable(); // bank branch name
            $table->string('branch_address', 255)->nullable(); // bank branch address
            $table->string('currency', 255)->nullable(); // currency used by this dealer
            $table->string('swift_code', 255)->nullable(); // swift code for international transactions
            $table->string('iban', 255)->nullable(); // iban for international transactions
            $table->unsignedBigInteger('created_by')->default(2); // foreign key referencing the user who created this
            $table->enum('status', ['pending', 'Approved', 'Rejected'])->default('pending');  // status of the application
            $table->tinyInteger('is_submitted')->default(0); // whether it has been submitted to admin or not
            $table->string('reason', 255)->nullable(); // reason why it was rejected, null otherwise
            $table->timestamp('deleted_at')->nullable(); // deleted at timestamp
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
        Schema::dropIfExists('dealers');
    }
};
