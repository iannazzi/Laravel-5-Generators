<?php

namespace Iannazzi\Generators\Migrations;

use Iannazzi\Generators\BaseGenerator;
use Iannazzi\Generators\Migrations\NameParser;
use Iannazzi\Generators\Migrations\SchemaParser;
use Iannazzi\Generators\Migrations\SyntaxBuilder;

class MigrationGenerator extends BaseGenerator
{
    protected $migration_name;
    protected $meta;
    protected $fields_from_xetrhon;
    protected $composer;

    public function makeMigrationFromCommand($migration_name, $migration_path, $schema)
    {
        //pass in the path and the file name, preferably the schema as well
        $this->makeMigration($migration_name, $migration_path, $schema);
    }

    public function makeMigrationFromExistingDatabase($migration_filename, $connection)
    {
        //make a migration file for a table using the DB connection to get the schema
        //this is the one that needs a map to change table names, add remove
        //columns
    }

    public function makeMigration($migration_name, $migration_path, $schema)
    {
        $this->migration_name = $migration_name;


        $migration_filename = $migration_path . '/' . $this->getMigrationFileName($migration_name);
        if ($this->files->exists($migration_filename))
        {
            dd($migration_filename . ' already exists!');
        }
        $this->makeDirectory($migration_filename);

        $compileMigrationStub = $this->compileMigrationStub($migration_name, $schema);

        dd($compileMigrationStub);
        $this->files->put($migration_filename, $compileMigrationStub);

        $this->command->info($migration_name . ' migration created successfully.');

    }
    protected function getMigrationFileName($migration_name)
    {

        return  date('Y_m_d_His') . '_' . $migration_name . '.php';
    }
    protected function compileMigrationStub($migration_name, $schema)
    {

        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
        $table_name = getTableNameFromMigrationName($migration_name);
        $this->replaceClassName($stub, $migration_name)
            ->replaceSchema($stub, $schema)
            ->replaceTableName($stub, $table_name);

        return $stub;
    }

    protected function replaceClassName(&$stub, $migration_name)
    {
        $className = ucwords(camel_case($migration_name));

        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }
    protected function replaceTableName(&$stub, $migration_name)
    {
        $stub = str_replace('{{table}}', $table, $stub);

        return $this;
    }
    protected function replaceSchema(&$stub, $schema)
    {

        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        return $this;
    }


}