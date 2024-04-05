<?php

namespace Database\Seeders;

use App\Models\Sidebar;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UpdateSidebarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $check = Sidebar::where('title',__('Product & Service'))->where('type','company')->exists();
        if($check){
            $siderbar = Sidebar::where('title',__('Product & Service'))->where('type','company')->first();
            $siderbar->update([
                'title' => "Vehicle",
                'icon' => 'ti ti-car',
                'parent_id' => 0,
                'sort_order' => 150,
                'route' => 'product-service.index',
                'module' => 'ProductService',
                'type' => 'company',
                'permissions' => 'product&service manage',
            ]);
        }
    }
}
