<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'simple command to run temporary development and debugging code';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // add code here

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
