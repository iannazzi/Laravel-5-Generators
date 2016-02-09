<?php

namespace Iannazzi\Generators\Migrations;

use File;
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
        $this->removemigrations($migration_path, $map);

        $schemaGenerator = new SchemaGenerator($connection, true, true);
        foreach($map['tables'] as $table => $table_map)
        {
            if (isset($map['tables'][$table]['make_migration_table']))
            {
                continue;
            }
            $table_name = $map['tables'][$table]['new_name'];

            $this->output->writeln('Creating Migration for Table: ' . $table_name);

            $fields = $schemaGenerator->getFields($table);
            $fields = $this->dropFields($table, $fields, $map);


            //$fields = $this->runFunctionOnFields($fields , $map);
            $fields = $this->renameFields($table, $fields, $map);

            $schema = (new SchemaParser)->parseFields($fields);
            $meta['action'] = 'create';
            $meta['table'] = $table_name;
            $schema = (new SyntaxBuilder)->create($schema, $meta);
            $schema = $this-> modifySchema($schema ,$table_name);
            $migration_name = 'create_' . $table_name . '_table';
            //$compileMigrationStub = $this->compileMigrationStub($migration_name, $schema);

            $this->makeMigration($table_name, $migration_name, $migration_path, $schema);
        }




    }
    public function makeMigration($table_name,$migration_name, $migration_path, $schema)
    {
        $this->migration_name = $migration_name;
        $migration_filename = $migration_path . '/' . $this->getMigrationFileName($migration_name);
        if ($this->files->exists($migration_filename))
        {
            dd($migration_filename . ' already exists!');
        }
        $this->makeDirectory($migration_filename);

        $compileMigrationStub = $this->compileMigrationStub($table_name, $migration_name, $schema);

        $this->files->put($migration_filename, $compileMigrationStub);

        $this->output->writeln($migration_name . ' migration created successfully.');

    }
    public function removemigrations($path, $map)
    {
        $files = File::files($path);
        foreach($files as $file)
        {
            foreach($map['tables'] as $original_name => $new_array )
            {
                $migration_name = 'create_' . $new_array['new_name'] . '_table';
                if(strpos(basename($file), $migration_name) !== false)
                {
                    $this->output->writeln('delete_file' . basename($file));
                    File::delete($file);
                }

            }
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
            $return_array[] = $this->renameField($table, $map, $field);
        }
        return $return_array;
    }
    private function renameField($table, $map, $field)
    {
        if(is_array( $field['field']))
        {

            for($i=0;$i<sizeof($field['field']);$i++)
            {
                if(array_key_exists($field['field'][$i], $map['tables'][ $table ]['rename_columns']))
                {
                    $field['field'][ $i ] = $map['tables'][ $table ]['rename_columns'][ $field['field'][ $i ] ];
                }
            }
            return $field;
        }
        if (array_key_exists($field['field'], $map['tables'][ $table ]['rename_columns']))
        {
            $field['field'] = $map['tables'][ $table ]['rename_columns'][ $field['field'] ];
            return $field;

        }
        return $field;
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
        $this->output->writeln('Modifying Schema... Have no Idea what is going on with indexes ');
        $schema = str_replace('pos_', '', $schema);
        $schema = str_replace('manufacturer_brand', 'brand', $schema);
        $schema = str_replace('purchase_order', 'po', $schema);
        $schema = str_replace("\$table->unique(['promotion_id','product_id','product_category_id'],'promotion_id');", '', $schema);
        $schema = str_replace("\$table->unique(['local_tax_jurisdiction_id','state_tax_jurisdiction_id'],'local_tax_jurisdiction_id');", '', $schema);

         $schema = str_replace("\$table->unique(['state_regular_sales_tax_rate_id','state_exemption_sales_tax_rate_id'],'state_regular_sales_tax_rate_id');", '', $schema);
        $schema = str_replace("\$table->unique(['sales_invoice_id','customer_payment_id'],'sales_invoice_id');",'',$schema);
        $schema = str_replace("\$table->unique(['sales_invoice_id','promotion_id'],'sales_invoice_id');",'',$schema);
        $schema = str_replace("\$table->unique(['default_gift_card_account_id','default_store_credit_account_id','default_prepay_account_id'],'default_gift_card_account_id');",'',$schema);
        $schema = str_replace("\$table->unique(['first_name','last_name'],'first_name');",'',$schema);
        $schema = str_replace("\$table->unique(['product_image_id','product_id'],'product_image_id');",'',$schema);


        //this one fucks up pos_sales_tax_category_id and purchase order categry id $schema = str_replace('category_id', 'product_category_id', $schema);
        //$table = rtrim($table, "s");
        //$schema = str_replace($table .'_', '', $schema);
        return $schema;

    }

    protected
    function getMigrationFileName($migration_name)
    {
        return date('Y_m_d_His') . '_' . $migration_name . '.php';
    }

    protected
    function compileMigrationStub($table_name, $migration_name, $schema)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
        $name_parser = new NameParser();
        //$table_name = $name_parser->getTableNameFromMigrationName($migration_name);
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