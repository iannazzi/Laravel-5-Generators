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

    function makeMigrationFromExistingDatabase($connection, $migration_path, $map)
    {


        $schemaGenerator = new SchemaGenerator($connection, false, false);
        foreach($map['tables'] as $table => $table_map)
        {
            $new_table = $map['tables'][$table]['new_name'];

            $this->output->writeln('Creating Migration for Table: ' . $new_table);

            $fields = $schemaGenerator->getFields($table);
            $fields = $this->dropFields($table, $fields, $map);


            //$fields = $this->runFunctionOnFields($fields , $map);
            $fields = $this->renameFields($table, $fields, $map);

            $schema = (new SchemaParser)->parseFields($fields);
            dd($schema);
            $meta['action'] = 'create';
            $meta['table'] = $new_table;
            $schema = (new SyntaxBuilder)->create($schema, $meta);
            $schema = $this-> modifySchema($schema ,$new_table);
            $migration_name = 'create_' . $new_table . '_table';
            //$compileMigrationStub = $this->compileMigrationStub($migration_name, $schema);

            $this->makeMigration($migration_name, $migration_path, $schema);
        }




    }

    public function dropFields($table, $fields, $map)
    {
        if ( ! isset($map['tables'][$table]['drop_columns']))
        {
            return $fields;
        }
        $mapped_fields = [];
        foreach ($fields as $field)
        {
            if ( ! in_array($field['field'], $map['tables'][$table]['drop_columns']))
            {
                $mapped_fields[] = $field;
            }
        }

        return $mapped_fields;
    }

    public function renameFields($table, $fields, $map)
    {
        if ( ! isset($map['tables'][$table]['rename_columns']))
        {

            return $fields;
        }
        $return_array = [];
        foreach ($fields as $field)
        {
            if (in_array($field['field'], $map['tables'][$table]['rename_columns']))
            {
                dd($map['rename_columns'][ $field['field'] ]);
                $field['field'] = $map['rename_columns'][ $field['field'] ];
                $return_array[] = $field;
            }
        }

        return $return_array;
    }
    public function runFunctionOnFields($fields, $map)
    {
        $new_array = [];
        foreach ($fields as $field)
        {
            $field['field'] = str_replace('pos_', '', $field['field']);
            $field['field'] = str_replace('manufacturer_brand', 'brand', $field['field']);
            $new_array[ ] = $field;
        }

        return $new_array;
    }
    public function modifySchema($schema, $table)
    {

        $schema = str_replace('pos_', '', $schema);
        $schema = str_replace('manufacturer_brand', 'brand', $schema);
        //$table = rtrim($table, "s");
        //$schema = str_replace($table .'_', '', $schema);
        return $schema;

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

        $this->files->put($migration_filename, $compileMigrationStub);

        $this->output->writeln($migration_name . ' migration created successfully.');

    }

    protected
    function getMigrationFileName($migration_name)
    {
        return date('Y_m_d_His') . '_' . $migration_name . '.php';
    }

    protected
    function compileMigrationStub($migration_name, $schema)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
        $name_parser = new NameParser();
        $table_name = $name_parser->getTableNameFromMigrationName($migration_name);
        $this->replaceClassName($stub, $migration_name)
            ->replaceSchema($stub, $schema)
            ->replaceTableName($stub, $table_name);

        return $stub;
    }

    protected
    function replaceClassName(&$stub, $migration_name)
    {
        $className = ucwords(camel_case($migration_name));

        $stub = str_replace('{{class}}', $className, $stub);

        return $this;
    }

    protected
    function replaceTableName(&$stub, $table_name)
    {
        $stub = str_replace('{{table}}', $table_name, $stub);

        return $this;
    }

    protected
    function replaceSchema(&$stub, $schema)
    {
        $stub = str_replace(['{{schema_up}}', '{{schema_down}}'], $schema, $stub);

        return $this;
    }


}