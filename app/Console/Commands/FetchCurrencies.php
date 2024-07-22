<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FetchCurrenciesService;

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

    protected $fetchCurrenciesService;

    public function __construct(FetchCurrenciesService $fetchCurrenciesService)
    {
        parent::__construct();
        $this->fetchCurrenciesService = $fetchCurrenciesService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $message = $this->fetchCurrenciesService->fetchAndUpdateCurrencies();
        $this->info($message);
        return 0;
    }
}
