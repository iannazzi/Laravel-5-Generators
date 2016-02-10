<?php  namespace Iannazzi\Generators\Commands;
use Iannazzi\Generators\DatabaseImporter\DatabaseFactoryCreator;
use Illuminate\Console\Command;

class CreateFactoriesFromDatabaseCommand extends Command
{

    protected $signature = 'zz:CreateFactoriesFromDatabase
                                {dbc=POS : The Database Connection}';
    protected $description = 'Create Factory Files For Craiglorious and Tenant Systems from POS connection';

    public function handle()
    {
        $dbc = $this->argument('dbc');

        DatabaseFactoryCreator::makeFactories($dbc);

    }

}
?>