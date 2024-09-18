<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sidebar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Install extends Command
{
    protected $signature = 'installer:run';
    protected $description = 'Installer command.';

    public function handle()
    {
        $this->info('# adding sidebar items for supplier reports.');

        $accounting_parent = Sidebar::where('title', __('Accounting'))->where('parent_id', 0)->where('type', 'company')->first();
        $report_parent = Sidebar::where('title', __('Report'))->where('parent_id', $accounting_parent->id)->where('type', 'company')->first();

        $supplier_report = Sidebar::where('title', __('Supplier Report'))->where('parent_id', $report_parent->id)->where('type', 'company')->first();
        if ($supplier_report == null) {
            Sidebar::create([
                'title' => 'Supplier Report',
                'icon' => '',
                'parent_id' => $report_parent->id,
                'sort_order' => 25,
                'route' => 'supplier.report',
                'permissions' => 'report manage',
                'module' => 'Account',
                'type' => 'company',
            ]);
        }

        $this->info('# running migrations.');

        // fix numeric value issue in tables
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_10_072315_update_numeric_fields_in_tables.php',
        ]);

        // added purchased_from column in product_services table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_08_29_101106_alter_product_services_table.php',
        ]);

        // added currency column in purchase_products table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_09_103533_alter_purchase_products_table.php',
        ]);

        // added currency column in transactions table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_09_105249_alter_transactions_table.php',
        ]);

        // added currency column in journal_entries table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_10_064107_alter_journal_entries_table.php',
        ]);

        // added currency column in purchase_payments table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_10_112656_alter_purchase_payments_table.php',
        ]);

        // added currency column in invoice_products table
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_09_16_053911_alter_invoice_products_table.php',
        ]);

        $this->info('# updating chart of accounts.');

        try {

            // update the tax expense chart of account
            if (DB::table('chart_of_account_sub_types')->where('name', '=', 'Tax Expense')->first() == null) {
                DB::table('chart_of_account_sub_types')->insert([
                    'name' => 'Tax Expense',
                    'type' => 6, // Expense
                    'workspace' => 1,
                    'created_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $tax_expense = DB::table('chart_of_account_sub_types')->where('name', '=', 'Tax Expense')->first();

            $purchase_tax = DB::table('chart_of_accounts')->where('name', '=', 'Purchase Tax')->first();
            if ($purchase_tax != null) {
                DB::table('chart_of_accounts')
                    ->where('id', $purchase_tax->id)
                    ->update([
                        'type' => 6, // Expense
                        'sub_type' => $tax_expense->id,
                    ]);
            }

            // Create or fetch KES CASH ACCOUNT
            $kes_cash_account = DB::table('chart_of_accounts')->where('name', '=', 'KES CASH ACCOUNT')->first();
            if ($kes_cash_account == null) {
                DB::table('chart_of_accounts')->insert([
                    'name' => 'KES CASH ACCOUNT',
                    'code' => 1061,
                    'type' => 1, // Asset
                    'sub_type' => 1,
                    'is_enabled' =>  1,
                    'description' => null,
                    'workspace' => 1,
                    'created_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $kes_cash_account = DB::table('chart_of_accounts')->where('name', '=', 'KES CASH ACCOUNT')->first(); // Fetch after insert
            }

            // Create or fetch USD CASH ACCOUNT
            $usd_cash_account = DB::table('chart_of_accounts')->where('name', '=', 'USD CASH ACCOUNT')->first();
            if ($usd_cash_account == null) {
                DB::table('chart_of_accounts')->insert([
                    'name' => 'USD CASH ACCOUNT',
                    'code' => 1062,
                    'type' => 1, // Asset
                    'sub_type' => 1,
                    'is_enabled' =>  1,
                    'description' => null,
                    'workspace' => 1,
                    'created_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $usd_cash_account = DB::table('chart_of_accounts')->where('name', '=', 'USD CASH ACCOUNT')->first(); // Fetch after insert
            }

            // Create or fetch KES EQUITY ACCOUNT
            $kes_equity_account = DB::table('chart_of_accounts')->where('name', '=', 'KES EQUITY ACCOUNT')->first();
            if ($kes_equity_account == null) {
                DB::table('chart_of_accounts')->insert([
                    'name' => 'KES EQUITY ACCOUNT',
                    'code' => 1063,
                    'type' => 3, // Equity
                    'sub_type' => 8,
                    'is_enabled' =>  1,
                    'description' => null,
                    'workspace' => 1,
                    'created_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $kes_equity_account = DB::table('chart_of_accounts')->where('name', '=', 'KES EQUITY ACCOUNT')->first(); // Fetch after insert
            }

            // Create or fetch USD EQUITY ACCOUNT
            $usd_equity_account = DB::table('chart_of_accounts')->where('name', '=', 'USD EQUITY ACCOUNT')->first();
            if ($usd_equity_account == null) {
                DB::table('chart_of_accounts')->insert([
                    'name' => 'USD EQUITY ACCOUNT',
                    'code' => 1064,
                    'type' => 3, // Equity
                    'sub_type' => 8,
                    'is_enabled' =>  1,
                    'description' => null,
                    'workspace' => 1,
                    'created_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $usd_equity_account = DB::table('chart_of_accounts')->where('name', '=', 'USD EQUITY ACCOUNT')->first(); // Fetch after insert
            }

            // find bank accounts in the bank_accounts table by using their account_number and then update thier chart_of_account_id
            $kes_cash_bank_account = DB::table('bank_accounts')->where('account_number', '3000176744')->first();
            if ($kes_cash_bank_account != null) {
                DB::table('bank_accounts')
                ->where('id', $kes_cash_bank_account->id)
                ->update(['chart_account_id' => $kes_cash_account->id]);
            }
            
            $usd_cash_bank_account = DB::table('bank_accounts')->where('account_number', '3001176750')->first();
            if ($usd_cash_bank_account != null) {
                DB::table('bank_accounts')
                ->where('id', $usd_cash_bank_account->id)
                ->update(['chart_account_id' => $usd_cash_account->id]);
            }

            $kes_equity_bank_account = DB::table('bank_accounts')->where('account_number', '0770285366501')->first();
            if ($kes_equity_bank_account != null) {
                DB::table('bank_accounts')
                ->where('id', $kes_equity_bank_account->id)
                ->update(['chart_account_id' => $kes_equity_account->id]);
            }
            
            $usd_equity_bank_account = DB::table('bank_accounts')->where('account_number', '0770285513198')->first();
            if ($usd_equity_bank_account != null) {
                DB::table('bank_accounts')
                ->where('id', $usd_equity_bank_account->id)
                ->update(['chart_account_id' => $usd_equity_account->id]);
            }
            

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }

        $this->info('# running optimization commands.');
        $this->call('optimize:clear');
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        $this->info('# finished.');
    }
}
