<?php namespace Iannazzi\Generators\DatabaseImporter;



use Iannazzi\Generators\Factories\FactoryGenerator;

class DatabaseFactoryCreator
{


    public static function makeFactories($dbc)
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


        $path = base_path() . '/tests/factories';
        $namespace = 'App\Models';

        $factory_generator = new FactoryGenerator();
        $tenant_namespace = $namespace . '\Tenant';
        $factory_generator->makeFactoriesFromExistingDatabase($dbc, $path, $tenant_namespace, $tenant_tables_map);

    }


}


	
