<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateNumericFieldsInTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify 'bank_accounts' table
        DB::statement('ALTER TABLE bank_accounts MODIFY opening_balance DOUBLE(15,2);');

        // Modify 'bank_transfer_payments' table
        DB::statement('ALTER TABLE bank_transfer_payments MODIFY price DOUBLE(15,2);');

        // Modify 'bank_transfers' table
        DB::statement('ALTER TABLE bank_transfers MODIFY amount DOUBLE(15,2);');

        // Modify 'bill_accounts' table
        DB::statement('ALTER TABLE bill_accounts MODIFY price DOUBLE(15,2);');

        // Modify 'bill_payments' table
        DB::statement('ALTER TABLE bill_payments MODIFY amount DOUBLE(15,2);');

        // Modify 'bill_products' table
        DB::statement('ALTER TABLE bill_products MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE bill_products MODIFY price DOUBLE(15,2);');

        // Modify 'coupons' table
        DB::statement('ALTER TABLE coupons MODIFY discount DOUBLE(15,2);');

        // Modify 'credit_notes' table
        DB::statement('ALTER TABLE credit_notes MODIFY amount DOUBLE(15,2);');

        // Modify 'customers' table
        DB::statement('ALTER TABLE customers MODIFY balance DOUBLE(15,2);');

        // Modify 'deals' table
        DB::statement('ALTER TABLE deals MODIFY price DOUBLE(15,2);');

        // Modify 'debit_notes' table
        DB::statement('ALTER TABLE debit_notes MODIFY amount DOUBLE(15,2);');

        // Modify 'employees' table
        DB::statement('ALTER TABLE employees MODIFY salary DOUBLE(15,2);');

        // Modify 'invoice_payments' table
        DB::statement('ALTER TABLE invoice_payments MODIFY amount DOUBLE(15,2);');

        // Modify 'invoice_products' table
        DB::statement('ALTER TABLE invoice_products MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE invoice_products MODIFY price DOUBLE(15,2);');

        // Modify 'journal_items' table
        DB::statement('ALTER TABLE journal_items MODIFY debit DOUBLE(15,2);');
        DB::statement('ALTER TABLE journal_items MODIFY credit DOUBLE(15,2);');

        // Modify 'payments' table
        DB::statement('ALTER TABLE payments MODIFY amount DOUBLE(15,2);');

        // Modify 'pos_payments' table
        DB::statement('ALTER TABLE pos_payments MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE pos_payments MODIFY discount_amount DOUBLE(15,2);');

        // Modify 'pos_products' table
        DB::statement('ALTER TABLE pos_products MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE pos_products MODIFY price DOUBLE(15,2);');

        // Modify 'product_services' table
        DB::statement('ALTER TABLE product_services MODIFY sale_price DOUBLE(15,2);');
        DB::statement('ALTER TABLE product_services MODIFY purchase_price DOUBLE(15,2);');

        // Modify 'proposal_products' table
        DB::statement('ALTER TABLE proposal_products MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE proposal_products MODIFY price DOUBLE(15,2);');

        // Modify 'purchase_debit_notes' table
        DB::statement('ALTER TABLE purchase_debit_notes MODIFY amount DOUBLE(15,2);');

        // Modify 'purchase_payments' table
        DB::statement('ALTER TABLE purchase_payments MODIFY amount DOUBLE(15,2);');

        // Modify 'purchase_products' table
        DB::statement('ALTER TABLE purchase_products MODIFY discount DOUBLE(15,2);');
        DB::statement('ALTER TABLE purchase_products MODIFY price DOUBLE(15,2);');

        // Modify 'revenues' table
        DB::statement('ALTER TABLE revenues MODIFY amount DOUBLE(15,2);');

        // Modify 'tax_deductions' table
        DB::statement('ALTER TABLE tax_deductions MODIFY salary_amount DOUBLE(15,2);');
        DB::statement('ALTER TABLE tax_deductions MODIFY difference DOUBLE(15,2);');
        DB::statement('ALTER TABLE tax_deductions MODIFY tax_deduction_value DOUBLE(15,2);');
        DB::statement('ALTER TABLE tax_deductions MODIFY tax_deduction_calculated DOUBLE(15,2);');

        // Modify 'tax_reliefs' table
        DB::statement('ALTER TABLE tax_reliefs MODIFY tax_relief_value DOUBLE(15,2);');

        // Modify 'transactions' table
        DB::statement('ALTER TABLE transactions MODIFY amount DOUBLE(15,2);');

        // Modify 'vendors' table
        DB::statement('ALTER TABLE vendors MODIFY balance DOUBLE(15,2);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse changes if necessary, using raw SQL or another approach.
    }
}
