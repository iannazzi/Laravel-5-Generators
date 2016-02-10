<?php

namespace Iannazzi\Generators\Commands;

use Artisan;
use Illuminate\Console\Command;

class MigrateCraigloriousCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zz:MigrateCraiglorious';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute Artisan Migration Command For Craiglorious System';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //php artisan migrate --path="database/migrations/craiglorious" --database ="craiglorious"

        //php artisan migrate --path="database/migrations/craiglorious" --database="test_craiglorious"
        Artisan::call('migrate', [
                '--path' => "database/migrations/craiglorious",
                '--database' => 'craiglorious',
            ]);
        Artisan::call('migrate', [
            '--path' => "database/migrations/craiglorious",
            '--database' => 'test_craiglorious',
        ]);


    }
}
