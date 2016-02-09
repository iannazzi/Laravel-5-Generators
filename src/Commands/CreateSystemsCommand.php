<?php

namespace Iannazzi\Generators\Commands;


use Artisan;
use Illuminate\Console\Command;

class CreateSystemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zz:CreateSystems
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
        $test = $this->option('test');
        Artisan::call('zz:CreateMigrationsFromDatabase', [

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
