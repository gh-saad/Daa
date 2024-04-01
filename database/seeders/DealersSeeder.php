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
        $numberOfDealers = 10;

        for ($i = 0; $i < $numberOfDealers; $i++) {
            $user = $users->random(); // Randomly assign a user

            Dealer::create([
                'user_id' => $user->id,
                'registration_no' => $faker->randomNumber(),
                'company_name' => $faker->company,
                'logo' => $faker->imageUrl(),
                'relational_manager' => $faker->randomElement([$user->id, null]),
                'website' => $faker->url,
                'company_whatsapp' => $faker->phoneNumber,
                'GM_whatsapp' => $faker->phoneNumber,
                'marketing_director_no' => $faker->phoneNumber,
                'trade_license' => $faker->word,
                'trno_expiry' => $faker->date(),
                'agency_license_number' => $faker->word,
                'trno_issue_place' => $faker->word,
                'po_box' => $faker->postcode,
                'trn_certificate' => $faker->word,
                'rera_certificate' => $faker->word,
                'passport' => $faker->word,
                'emirates_id' => $faker->word,
                'rara_card' => $faker->word,
                'brokage_agreement' => $faker->word,
                'is_agreement_signed' => $faker->boolean(),
                'bank_name' => $faker->word,
                'ac_name' => $faker->name,
                'branch_name' => $faker->word,
                'branch_address' => $faker->address,
                'currency' => $faker->currencyCode,
                'swift_code' => $faker->swiftBicNumber,
                'iban' => $faker->iban,
                'created_by' => $user->id,
                'status' => $faker->randomElement(['pending', 'Approved', 'Rejected']),
                'is_submitted' => $faker->boolean(),
                'reason' => $faker->sentence
            ]);
        }
    }
}
