<?php  namespace Iannazzi\Generators\Commands;
use Illuminate\Console\Command;
use Iannazzi\Generators\DatabaseImporter\DatabaseMigrationCreator;

class CreateMigrationsFromDatabaseCommand extends Command
{
	
    protected $signature = 'zz:CreateMigrationsFromDatabase
                                {dbc=POS : The Database Connection}';
    protected $description = 'Create Migration Files For Craiglorious and Tenant Systems from POS connection';

    public function handle()
    {
        $dbc = $this->argument('dbc');

        DatabaseMigrationCreator::makeMigrations($dbc);

    }

}
?>