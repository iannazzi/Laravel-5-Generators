<?php

namespace Iannazzi\Generators\Migrations;

use Iannazzi\Generators\BaseGenerator;
use Iannazzi\Generators\Migrations\NameParser;
use Iannazzi\Generators\Migrations\SchemaParser;
use Iannazzi\Generators\Migrations\SyntaxBuilder;
use Iannazzi\Generators\Migrations\SchemaGenerator;

class MigrationGenerator extends BaseGenerator
{

    public function makeMigrationFromCommand($migration_name, $migration_path, $schema)
    {
        //pass in the path and the file name, preferably the schema as well
        $this->makeMigration($migration_name, $migration_path, $schema);
    }

    public function makeMigrationFromExistingDatabase($connection, $migration_path, $table, $map)
    {
        //map looks like this:
//        $map = array(
//        'pre_table_insert' => ['class' => $this, 'method' => 'preTableInsertFunction'],
//            'tables'           => [
//        'pos_binders'                            =>
//            array(
//                'new_name' => 'binders',
//                'type'     => 'regular',
//            ),]);
        $schemaGenerator = new SchemaGenerator($connection, false, false);
        $fields = $schemaGenerator->getFields( $table );
        $schema = (new SchemaParser)->parseFields($fields);
        //dd($schema);




        //$schema = (new SchemaParser)->parseFields($fields);
        //dd($schema);
        //convert $fields
        //conver the fields to shchema
        $migration_name = 'create_' . $map['new_name'] . '_table.php';
        $compileMigrationStub = $this->compileMigrationStub($migration_name, $schema);

        dd($compileMigrationStub);
        //$this->makeMigration($migration_name, $migration_path, $fields);
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

        $this->output->writeln($migration_name . ' migration created successfully.');

    }
    protected function getMigrationFileName($migration_name)
    {
        return  date('Y_m_d_His') . '_' . $migration_name . '.php';
    }
    protected function compileMigrationStub($migration_name, $schema)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
        $name_parser = new NameParser();
        $table_name = $name_parser->getTableNameFromMigrationName($migration_name);
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
    protected function replaceTableName(&$stub, $table_name)
    {
        $stub = str_replace('{{table}}', $table_name, $stub);

        return $this;
    }
    protected function replaceSchema(&$stub, $schema)
    {
        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        return $this;
    }


}