<?php namespace Iannazzi\Generators\DatabaseImporter;

use Iannazzi\Generators\Migrations\MigrationGenerator;

class DatabaseMigrationCreator
{


    public static function makeMigrations($dbc)
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


        $path = base_path() . '/database/migrations';
        $path = base_path() . '/database/exports/migrations';

        $migration_generator = new MigrationGenerator();
        $tenant_migration_path = $path . '/tenant';
        $migration_generator->makeMigrationFromExistingDatabase($dbc, $tenant_migration_path, $tenant_tables_map);
        $cg_migration_path = $path . '/craiglorious';
        $migration_generator->makeMigrationFromExistingDatabase($dbc, $cg_migration_path, $craiglorious_tables_map);

    }


}


	
