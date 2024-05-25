<?php

namespace Modules\DoubleEntry\Database\Seeders;

use App\Models\Sidebar;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DoubleEntrySidebarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $userTypes = ['super admin', 'company', 'Accountant'];
        $userTypes = ['company'];
        
        foreach ($userTypes as $userType) {
            $this->seedSidebarForUserType($userType);
        }
    }

    /**
     * Seed sidebar items for a specific user type.
     *
     * @param string $userType
     * @return void
     */
    private function seedSidebarForUserType($userType)
    {
        // Find or create the main sidebar item for DoubleEntry based on user type
        $doubleEntrySidebar = Sidebar::updateOrCreate([
            'title' => 'DoubleEntry',
            'icon' => 'ti ti-scale',
            'parent_id' => 0,
            'sort_order' => 425,
            'route' => '',
            'permissions' => 'doubleentry manage',
            'type' => $userType,
            'module' => 'DoubleEntry',
        ]);

        // Add sub-menu items for DoubleEntry based on user type
        $this->addSubMenu($doubleEntrySidebar, 'Journal Account', 'journal-entry.index', 'journalentry manage');
        $this->addSubMenu($doubleEntrySidebar, 'Ledger Summary', 'report.ledger', 'report ledger');
        $this->addSubMenu($doubleEntrySidebar, 'Balance Sheet', 'report.balance.sheet', 'report balance sheet');
        $this->addSubMenu($doubleEntrySidebar, 'Profit & Loss', 'report.profit.loss', 'report profit loss');
        $this->addSubMenu($doubleEntrySidebar, 'Trial Balance', 'report.trial.balance', 'report trial balance');
        
        // Add a sub-menu for reports based on user type
        $reportSubMenu = $this->addSubMenu($doubleEntrySidebar, 'Report', '', 'report manage');
        $this->addSubMenu($reportSubMenu, 'Sales', 'report.sales', 'report sales');
        $this->addSubMenu($reportSubMenu, 'Receivables', 'report.receivables', 'report receivables');
        $this->addSubMenu($reportSubMenu, 'Payables', 'report.payables', 'report payables');
    }

    /**
     * Add a sub-menu item to the provided parent sidebar item.
     *
     * @param \App\Models\Sidebar $parent
     * @param string $title
     * @param string $route
     * @param string $permissions
     * @param string $icon
     * @param int $order
     * @return \App\Models\Sidebar
     */
    private function addSubMenu($parent, $title, $route, $permissions, $icon = '', $order = 10)
    {
        return Sidebar::updateOrCreate([
            'title' => $title,
            'icon' => $icon,
            'parent_id' => $parent->id,
            'sort_order' => $order,
            'route' => $route,
            'permissions' => $permissions,
            'type' => $parent->type, // Inherit the user type from the parent
            'module' => 'DoubleEntry',
        ]);
    }
}
