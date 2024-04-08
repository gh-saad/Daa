<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dealer;
use App\Models\User;
use Faker\Factory as Faker;

class DealersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get some users to associate with dealers
        $users = User::all();

        // Define number of dealers you want to create
        $numberOfDealers = 18;

        for ($i = 0; $i < $numberOfDealers; $i++) {
            $user = $users->random(); // Randomly assign a user

            Dealer::create([
                'user_id' => $user->id,
                'company_name' => $faker->company,
                'logo' => 'uploads/dealer-logos/default.png',
                'relational_manager' => $faker->randomElement($users)->id,
                'website' => $faker->url,
                'company_whatsapp' => $faker->phoneNumber,
                'GM_whatsapp' => $faker->phoneNumber,
                'marketing_director_no' => $faker->phoneNumber,
                'dealer_document' => $faker->imageUrl(),
                'passport_copy' => $faker->imageUrl(),
                'trade_license' => $faker->imageUrl(),
                'emirates_document' => $faker->imageUrl(),
                'tax_document' => $faker->imageUrl(),
                'security_cheque_copy' => $faker->imageUrl(),
                'po_box' => $faker->postcode,
                'is_agreement_signed' => $faker->boolean(),
                'bank_name' => $faker->word,
                'ac_name' => $faker->name,
                'branch_name' => $faker->company,
                'branch_address' => $faker->address,
                'currency' => $faker->currencyCode,
                'swift_code' => $faker->swiftBicNumber,
                'iban' => $faker->iban('AE'),
                'created_by' => 2, // You can change this to the desired user ID
                'status' => $faker->randomElement(['pending', 'Approved', 'Rejected']),
                'is_submitted' => $faker->boolean(),
                'reason' => $faker->sentence,
            ]);
        }
    }
}
