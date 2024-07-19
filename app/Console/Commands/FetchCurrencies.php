<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and seed currencies from openexchangerates.org';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiKey = 'afadb6bf6ec94881ae2506094fa4d6c0';
        $url = "https://openexchangerates.org/api/currencies.json?app_id={$apiKey}";

        $response = Http::get($url);

        if ($response->successful()) {
            $currencies = $response->json();

            foreach ($currencies as $code => $name) {
                DB::table('currency')->updateOrInsert(
                    ['code' => $code],
                    ['name' => $name, 'symbol' => ''] // Symbol data is not provided by the API,
                );
            }

            $this->info('Currencies have been successfully fetched.');
        } else {
            $this->error('Failed to fetch currencies. Please check your API key and try again.');
        }

        return 0;
    }
}
