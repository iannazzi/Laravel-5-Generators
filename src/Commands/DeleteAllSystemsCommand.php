<?php

namespace Iannazzi\Generators\Commands;

use Iannazzi\Generators\DatabaseImporter\DatabaseDestroyer;
use Artisan;
use Illuminate\Console\Command;
Use DB;

class DeleteAllSystemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zz:DeleteAllSystems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DANGER THIS EMPTIES CRAIGLORIOUS AND REMOVES ALL TENANT DATABASES';

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


        try
        {
            DB::connection('craiglorious')->statement('delete from systems where 1');
        } catch (\Illuminate\Database\QueryException $e)
        {
            echo 'error: craiglorious appears to be empty.... continue';
        }


        $databaseDestroyer = new DatabaseDestroyer();
        $databaseDestroyer->dropAllTables('craiglorious');

        echo 'Deleted systems from craiglroious ' . PHP_EOL;

        DatabaseDestroyer::deleteAllTenantDatabases();

        return;


    }
}
