<?php namespace Iannazzi\Generators\DatabaseImporter;

use Iannazzi\Generators\Migrations\MigrationGenerator;

class DatabaseMigrationCreator
{
    private $bhdbc;
    private $test;

    public function __construct($dbc, $test = false)
    {
        $this->test = $test;
        $this->bhdbc = $dbc;
    }

    public function getMigrationPath()
    {
        $path = base_path() . '/database/migrations';

        return $path;
    }

    public function makeMigrations()
    {

        //get the tables to migrate - I am migrating to two different migrations, one for
        //the main system, one for the tenant system.
        //You are probably just needing one migration. Re-write the table map for your use.
        $craiglorious_tables_map = DatabaseMigrationMap::getCraigloriousTablesFromBluehost();
        $tenant_tables_map = DatabaseMigrationMap::getTenantTablesFromBluehost();

        //to test a single table uncomment the lower lines and change pos_product_image_lookup to your
        //source table

//        $test = $tenant_tables_map['tables']['pos_product_image_lookup'];
//        $tenant_tables_map['tables'] = [];
//        $tenant_tables_map['tables']['pos_product_image_lookup'] =  $test;

        $migration_generator = new MigrationGenerator();
        $tenant_migration_path = $this->getMigrationPath() . '/tenant';
        $migration_generator->makeMigrationFromExistingDatabase($this->bhdbc, $tenant_migration_path, $tenant_tables_map);
        $cg_migration_path = $this->getMigrationPath() . '/craiglorious';
        $migration_generator->makeMigrationFromExistingDatabase($this->bhdbc, $cg_migration_path, $craiglorious_tables_map);

    }

    protected function makeMigrationTables($tables)
    {

        foreach ($tables['tables'] as $original_table_name => $new_table)
        {

            //map the table name
            $new_table_name = $new_table['new_name'];

            //what is the migration_name???????
            //$migration_filename = $this->createMigrationFileName($new_table_name);

            $migration_generator = new MigrationGenerator();

            $migration_generator->makeMigrationFromExistingDatabase($this->bhdbc, $migration_path, $original_table_name, $new_table);


            // $bluehostMigrationGenerator->makeMigration($migration_path,$migration_filename);


        }
    }

    protected function createMigrationFileName($table_name)
    {
        return 'create_' . $table_name . '_table.php';
    }


}


	
