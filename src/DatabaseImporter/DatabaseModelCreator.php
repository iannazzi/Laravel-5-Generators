<?php

namespace Iannazzi\Generators\DatabaseImporter;


use Iannazzi\Generators\Models\ModelGenerator;

class DatabaseModelCreator
{
    public static function getModelPath()
    {
        $path = app_path() . '/Models';
        $path = base_path() . '/database/exports/Models';
        return $path;
    }
    public static function getModelNamespace()
    {
        return 'App' . str_replace(app_path(), '', self::getModelPath());
    }

    public static function makeModels($dbc)
    {
        $craiglorious_tables_map = DatabaseMigrationMap::getCraigloriousTableMap();
        $tenant_tables_map = DatabaseMigrationMap::getTenantTableMap();

//        $test = $tenant_tables_map['tables']['pos_products'];
//        $tenant_tables_map['tables'] = [];
//        $tenant_tables_map['tables']['pos_products'] =  $test;

        $modelGenerator = new ModelGenerator();
        $tenant_migration_path = self::getModelPath() . '/Tenant';
        $modelGenerator->makeModelsFromExistingDatabase($dbc, $tenant_migration_path, $tenant_tables_map);
        $cg_migration_path = self::getModelPath() . '/Craiglorious';
        $modelGenerator->makeModelsFromExistingDatabase($dbc, $cg_migration_path, $craiglorious_tables_map);

    }
}