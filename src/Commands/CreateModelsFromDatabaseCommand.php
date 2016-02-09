<?php  namespace Iannazzi\Generators\Commands;
use Iannazzi\Generators\DatabaseImporter\DatabaseConnector;
use Iannazzi\Generators\DatabaseImporter\ModelCreator;
use Illuminate\Console\Command;


class CreateModelsFromDatabaseCommand extends Command
{

    protected $signature = 'zz:CreateModelsFromDatabase';
    protected $description = 'Create Model Files For Craiglorious and Tenant Systems from POS connection';
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
        $modelCreator = new ModelCreator('POS', $test);
        $modelCreator->makeModels();


    }

}
?>