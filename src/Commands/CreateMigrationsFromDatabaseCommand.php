<?php  namespace Iannazzi\Generators\Commands;
use Iannazzi\Generators\DatabaseImporter\DatabaseConnector;
use Illuminate\Console\Command;
use Iannazzi\Generators\DatabaseImporter\DatabaseMigrationCreator;

class CreateMigrationsFromDatabaseCommand extends Command
{
	
    protected $signature = 'zz:CreateMigrationsFromDatabase';
    protected $description = 'Create Migration Files For Craiglorious and Tenant Systems from POS connection';
	public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {

        DatabaseConnector::addConnections();

        //make table migration files
        //make models
        //make table defs
        //make factories
        //make seeds
        //inport the data

        //use ways generateor and xetrhon to help....

        //$t = new MigrationMakeCommand(new Filesystem());
        $test = true;
        $bluehost = new DatabaseMigrationCreator('POS', $test);
        $bluehost->makeMigrations();


    }

}
?>