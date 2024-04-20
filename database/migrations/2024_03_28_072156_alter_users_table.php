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
        Schema::table('users', function (Blueprint $table) {
            $table->string('member_id')->nullable();
            $table->integer('role')->nullable();
            $table->string('company_name')->nullable();
            $table->string('authentication_ref_id')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();
            $table->string('address1')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('gender')->nullable();
            $table->string('personal_access_token')->nullable();
            $table->string('token_expires_at')->nullable();
            $table->integer('balance_privilege_point')->nullable();
            $table->string('contract-status')->nullable()->after('email_verified_at');
            $table->integer('balance-amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('member_id');
            $table->dropColumn('role');
            $table->dropColumn('company_name');
            $table->dropColumn('authentication_ref_id');
            $table->dropColumn('contact_no');
            $table->dropColumn('address');
            $table->dropColumn('address1');
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('zip_code');
            $table->dropColumn('gender');
            $table->dropColumn('personal_access_token');
            $table->dropColumn('token_expires_at');
            $table->dropColumn('balance_privilege_point');
            $table->dropColumn('contract-status');
            $table->dropColumn('balance-amount');
        });
    }
};
