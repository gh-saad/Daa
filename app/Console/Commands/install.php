<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sidebar;
use Illuminate\Support\Facades\DB;

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

        $this->info('# updating chart of accounts.');

        try {
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
