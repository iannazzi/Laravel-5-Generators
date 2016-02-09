<?php

namespace Iannazzi\Generators\DatabaseImporter;


class SeederCreator
{


    public function getSeedTables($db_name)
    {
        $databaseMigrator = new DatabaseMigrationCreator($this->test);

        $migration_files = $databaseMigrator->getMigrationFiles($db_name);

        $migration_tables = $databaseMigrator->getMigrationTableName($migration_files);

        $startup_data_tables = $this->getStartupDataTables($db_name);

        //$databaseSeeder = new DatabaseSeeder($this->test);
        $tables_to_seed = DatabaseSeederCreator::selectTablesToSeed($migration_tables, $startup_data_tables);

        return $tables_to_seed;
    }


    public function getStartupDataTables($db_name)
    {
        $databaseSeeder = new DatabaseSeederCreator($this->test);
        $startup_data_tables = $databaseSeeder->getStartupCSVTableNames($db_name);

        return $startup_data_tables;
    }

    public function getStartupSeedFiles($db)
    {
        //$db is tenant or craiglorious
        $directory = database_path("seeds/csv_startup_data/" . $db);
        $files = \File::allFiles($directory);

        return $files;
    }

    public function getStartupCSVTableNames($db)
    {
        $startup_data_files = $this->getStartupSeedFiles($db);

        $tables = [];
        foreach ($startup_data_files as $file)
        {
            //get the name and make it a seeder...
            $table = basename((string) $file, '.csv');
            $tables[] = $table;
        }

        return $tables;
    }

    public static function selectTablesToSeed($migration_tables, $startup_data_tables)
    {
        $tables_to_seed = [];

        foreach ($migration_tables as $table)
        {
            if ( ! in_array($table, $startup_data_tables))
            {
                $tables_to_seed[] = $table;
            }
        }

        return $tables_to_seed;
    }


}