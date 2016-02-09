<?php  namespace Iannazzi\Generators\Commands;
use Iannazzi\Generators\DatabaseImporter\DatabaseConnector;
use Iannazzi\Generators\DatabaseImporter\DatabaseModelCreator;
use Iannazzi\Generators\DatabaseImporter\ModelCreator;
use Illuminate\Console\Command;


class CreateModelsFromDatabaseCommand extends Command
{

    protected $signature = 'zz:CreateModelsFromDatabase
                                    {dbc=POS : The Database Connection}';

    protected $description = 'Create Model Files For Craiglorious and Tenant Systems from POS connection';

    public function handle()
    {


        $dbc = $this->argument('dbc');
        DatabaseModelCreator::makeModels($dbc);


    }

}
?>