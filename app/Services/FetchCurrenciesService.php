<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FetchCurrenciesService
{
    public function fetchAndUpdateCurrencies()
    {
        $apiKey = 'afadb6bf6ec94881ae2506094fa4d6c0';

        // Fetch currencies
        $currenciesUrl = "https://openexchangerates.org/api/currencies.json?app_id={$apiKey}";
        $currenciesResponse = Http::get($currenciesUrl);

        if ($currenciesResponse->successful()) {
            $currencies = $currenciesResponse->json();

            // Fetch exchange rates
            $ratesUrl = "https://openexchangerates.org/api/latest.json?app_id={$apiKey}";
            $ratesResponse = Http::get($ratesUrl);

            if ($ratesResponse->successful()) {
                $rates = $ratesResponse->json()['rates'];

                foreach ($currencies as $code => $name) {
                    $rate = $rates[$code] ?? null; // Get the rate if available

                    // if currency code exist in database table update rate from fetched data and nothing else
                    if (DB::table('currency')->where('code', $code)->exists()) {
                        DB::table('currency')->where('code', $code)->update(['rate' => $rate]);
                    }else{
                        // if currency code not exist in database table insert new currency with fetched data
                        DB::table('currency')->insert(['code' => $code, 'name' => $name, 'rate' => $rate, 'symbol' => $code]);
                    }
                }

                DB::table('currency')->update(['last_fetched' => Carbon::now()]);

                return 'Currencies and rates have been successfully fetched.';
            } else {
                return 'Failed to fetch exchange rates. Please check your API key and try again.';
            }
        } else {
            return 'Failed to fetch currencies. Please check your API key and try again.';
        }
    }
}
