<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [];
        
        for ($i = 1; $i <= 10; $i++) {
            $customers[] = [
                'customer_id' => $i,
                'user_id' => null,
                'name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'password' => bcrypt('password123'),
                'contact' => '123456789' . $i,
                'tax_number' => '123456789' . $i,
                'billing_name' => 'Customer ' . $i,
                'billing_country' => 'USA',
                'billing_state' => 'California',
                'billing_city' => 'Los Angeles',
                'billing_phone' => '123456789' . $i,
                'billing_zip' => '9000' . $i,
                'billing_address' => $i . ' Main St',
                'shipping_name' => 'Customer ' . $i,
                'shipping_country' => 'USA',
                'shipping_state' => 'California',
                'shipping_city' => 'Los Angeles',
                'shipping_phone' => '123456789' . $i,
                'shipping_zip' => '9000' . $i,
                'shipping_address' => $i . ' Main St',
                'lang' => 'en',
                'balance' => 0.00,
                'workspace' => null,
                'created_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('customers')->insert($customers);
    }
}

