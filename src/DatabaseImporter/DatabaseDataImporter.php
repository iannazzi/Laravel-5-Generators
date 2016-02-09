<?php

namespace Iannazzi\Generators\DatabaseImporter;


use Iannazzi\Generators\DatabaseImporter\ArrayOperator;
use App\Classes\Database\DatabaseModifier;
use App\Models\Craiglorious\System;
use DB;
use Schema;

class DatabaseDataImporter
{
    use DatabaseImporterTrait;
    protected $test;
    protected $databaseModifier;

    public function __construct($source_connection, $test)
    {
        $this->test = $test;
        $this->source_connection = $source_connection;
        $this->databaseModifier = new DatabaseModifier($test);
    }

    public function importEmbrasseMoiData()
    {
        $system = System::where('company', '=', 'Embrasse-Moi')->first();

        if ( ! $system)
        {
            dd('did you create company named Embrasse-Moi?');
        }
        $system->createTenantConnection();
        echo 'Importing Data From Database: ' . $system->getDBC() . PHP_EOL;


        $craiglorious_tables_map = DatabaseMigrationMap::getCraigloriousTablesFromBluehost();
        $tenant_tables_map = DatabaseMigrationMap::getTenantTablesFromBluehost();

//        $test = $tenant_tables_map['tables']['pos_accounts'];
//        $tenant_tables_map['tables'] = [];
//        $tenant_tables_map['tables']['pos_accounts'] =  $test;


        $this->importTables($this->source_connection, $system->getDBC(), $tenant_tables_map);
        $this->importTables($this->source_connection, 'craiglorious', $craiglorious_tables_map);


    }

    public function importTables($source_dbc, $dest_dbc, array $map)
    {
        foreach ($map['tables'] as $source_table => $dest_table)
        {
            $new_table = $dest_table['new_name'];

            $this->importTable($source_dbc, $source_table, $dest_dbc, $new_table, $map);
        }
    }

    public function importTable($source_dbc, $source_table, $dest_dbc, $dest_table, array $map)
    {

        if ( ! Schema::Connection($source_dbc)->hasTable($source_table))
        {
            $this->console($source_dbc . ' does not have this table: ' . $source_table);

            return;
        }
        $this->console('Copying Table: Table ' . $source_table . ' Found on DB Connection ' . $source_dbc);
        $this->copyTable($source_dbc, $source_table, $dest_dbc, $dest_table, $map);
        $this->generateNewData($source_table, $dest_dbc, $map);

    }

    public function copyTable($souce_connection, $source_table, $dest_connection, $dest_table, $map)
    {
        $this->databaseModifier->emptyTable($dest_connection, $dest_table);
        if ( ! isset($map['tables'][ $source_table ]['import_data']))
        {
            $num_chunk_records = 1000;
            $me = $this;
            $msg = 'Seeding MAX ' . $num_chunk_records . ' from: ' . $source_table . ' to table ' . $dest_table;
            $this->console('OUTPUT: ' . $msg);
            DB::connection($souce_connection)->table($source_table)->chunk($num_chunk_records, function ($data_chunk)
            use ($me, $dest_connection, $source_table, $dest_table, $map, $num_chunk_records)
            {
                //migrateTableColumns($data, $table, $migration_map)
                $data_chunk = $this->preEntryMap($data_chunk, $source_table, $dest_table, $map);
                $this->databaseModifier->loadDataIntoTable($dest_connection, $dest_table, $data_chunk);
                if ($this->test) return false;

            });
        }
    }

    public function preEntryMap($data, $source_table, $dest_table, $map)
    {

        if (isset($map['tables'][ $source_table ]['drop_columns']))
        {
            $drop_columns = $map['tables'][ $source_table ]['drop_columns'];

            $data = ArrayOperator::dropColumns($data, $drop_columns);
        }


        if (isset($map['tables'][ $source_table ]['rename_columns']))
        {
            $renameColumns = $map['tables'][ $source_table ]['rename_columns'];
            $arrayOperator = new ArrayOperator();
            $data = $arrayOperator->renameColumns($data, $renameColumns);
        }

        $data = $this->executePreFunction($data);

        $data = $this->modifyData($data, $source_table, $map);

        return $data;

    }

    public static function executePreFunction($data)
    {
        $new_array = [];
        for ($i = 0; $i < sizeof($data); $i ++)
        {
            foreach ($data[ $i ] as $key => $value)
            {
                $key = str_replace('pos_', '', $key);
                $key = str_replace('manufacturer_brand', 'brand', $key);
                $new_array[ $i ][ $key ] = $value;
            }
        }

        return $new_array;
    }


    public function modifyData($data, $source_table, $map)
    {

        if (isset($map['tables'][ $source_table ]['modify_data_function']))
        {

            $data = $map['tables'][ $source_table ]['modify_data_function']($data);

            return $data;
        }


        return $data;
    }

    public function generateNewData($source_table, $dest_dbc, $map)
    {

        if (isset($map['tables'][ $source_table ]['generate_data_function']))
        {
            $map['tables'][ $source_table ]['generate_data_function']();

        }


    }

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


    //these appear to be dead functions
    public function seedTableData($table, array $seed_map)
    {
        dd('dead function seedTableData?');
        foreach ($seed_map as $table_to_modify => $seed_code)
        {
            if ($table == $table_to_modify)
            {
                if (isset($seed_map['seed_columns']))
                {
                    $columns = $seed_map['seed_columns'];
                    $this->seedDataColumns($table, $columns);

                }
            }
        }
    }

    public function seedDataColumns($table, array $populate_columns)
    {
        dd('dead function seedDataColumns?');
        for ($i = 0; $i < sizeof($data); $i ++)
        {
            $data[ $i ] = $this->seedDataRowColumns($data[ $i ], $populate_columns);
        }

        return $data;
    }

    function seedDataRowColumns($data_row, array $populate_columns)
    {
        dd('dead function seedDataRowColumns?');

        foreach ($data_row as $column => $value)
        {
            $data_row[ $column ] = $this->seedColumnValue($column, $value, $populate_columns);
        }
    }

    function seedColumnValue($column, $value, array $populate_columns)
    {
        if (in_array($column, $populate_columns))
        {
            return eval($populate_columns[ $column ]);
        }

        return $value;
    }


}