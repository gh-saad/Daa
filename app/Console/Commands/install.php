<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sidebar;

class install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installer command.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 08/29/2024
        $this->info('# adding sidebar items for supplier reports.');

        // parent sidebar items
        $accounting_parent = Sidebar::where('title',__('Accounting'))->where('parent_id',0)->where('type','company')->first();
        $report_parent = Sidebar::where('title',__('Report'))->where('parent_id',$accounting_parent->id)->where('type','company')->first();

        // supplier report tab
        $supplier_report = Sidebar::where('title',__('Supplier Report'))->where('parent_id',$report_parent->id)->where('type','company')->first();
        if($supplier_report == null)
        {
            Sidebar::create( [
                    'title' => 'Supplier Report',
                    'icon' => '',
                    'parent_id' => $report_parent->id,
                    'sort_order' => 25,
                    'route' => 'supplier.report',
                    'permissions' => 'report manage',
                    'module' => 'Account',
                    'type'=>'company',
                ]);
        }

        // run migration
        $this->info('# running migrations.');
        $this->call('migrate', [
            '--path' => 'database/migrations/2024_08_29_101106_alter_product_services_table.php',
        ]);
        
        // optimization
        $this->info('# running optimization commands.');
        $this->call('optimize:clear');
        $this->call('view:clear');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('route:clear');
        
        // finish
        $this->info('# finished.');
    }
}
