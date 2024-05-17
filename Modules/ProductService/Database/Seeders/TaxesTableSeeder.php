<?php

namespace Modules\ProductService\Database\Seeders;

use Modules\ProductService\Entities\Tax;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $check = Tax::where('name',__('default'))->exists();
        if(!$check){
            Tax::create( [
                'id' => 1,
                'name' => __('default'),
                'rate' => 0,
                'created_by' => 1,
                'workspace_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}