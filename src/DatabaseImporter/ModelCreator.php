<?php

namespace Iannazzi\Generators\DatabaseImporter;


use Iannazzi\Generators\Migrations\ModelGenerator;

class ModelCreator
{
    public function getModelPath()
    {
        $path = app_path() . '/Models';
        return $path;
    }
    public function makeModels()
    {
        $craiglorious_tables_map = DatabaseMigrationMap::getCraigloriousTablesFromBluehost();
        $tenant_tables_map = DatabaseMigrationMap::getTenantTablesFromBluehost();

//        $test = $tenant_tables_map['tables']['pos_product_image_lookup'];
//        $tenant_tables_map['tables'] = [];
//        $tenant_tables_map['tables']['pos_product_image_lookup'] =  $test;

        $modelGenerator = new ModelGenerator();
        $tenant_migration_path = $this->getModelPath() . '/Tenant';
        $modelGenerator->makeModelsFromExistingDatabase($this->bhdbc, $tenant_migration_path, $tenant_tables_map);
        $cg_migration_path = $this->getModelPath() . '/Craiglorious';
        $modelGenerator->makeModelsFromExistingDatabase($this->bhdbc, $cg_migration_path, $craiglorious_tables_map);

    }
}