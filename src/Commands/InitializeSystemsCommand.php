<?php

namespace Iannazzi\Generators\Commands;


use Artisan;
use Illuminate\Console\Command;

class InitializeSystemsCommand extends Command
{
    //this should get me up and running????

    protected $signature = 'zz:InitializeSystems
                             {--test=false : If True I will import only 100 rows from the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Migrations, Make Migrations, Delete and create systems, Pull in Data';

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
        $db = 'POS';
        $test = $this->option('test');
        Artisan::call('zz:CreateMigrationsFromDatabase POS', [

        ]);
        Artisan::call('zz:DeleteAllSystems', [

        ]);
        Artisan::call('zz:MigrateCraiglorious', [

        ]);
        Artisan::call('zz:SeedCraiglorious', [

        ]);
        Artisan::call('zz:ImportDatabase', [
            '--test' => $test,
        ]);

    }
}
