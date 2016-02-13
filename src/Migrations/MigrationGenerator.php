<?php

namespace Iannazzi\Generators\Migrations;

use Iannazzi\Generators\BaseGenerator;

class MigrationGenerator extends BaseGenerator
{
    function makeMigrationFromExistingDatabase($dbc, $migration_path, $map)
    {
        $this->removeFiles($migration_path, $map, function ($name)
        {
            return self::getMigrationName($name);

        });


        foreach ($map['tables'] as $table => $table_map)
        {
            if (isset($map['tables'][ $table ]['make_migration_table']))
            {
                //although this says continue, we are skipping this migration
                continue;
            }
            $table_name = $map['tables'][ $table ]['new_name'];

            $this->output->writeln('Creating Migration for Table: ' . $table_name);

            $fields = $this->getFields($dbc,$table, $map);


            $schema = (new SchemaParser)->parseFields($fields);
            $meta['action'] = 'create';
            $meta['table'] = $table_name;
            $schema = (new SyntaxBuilder)->create($schema, $meta);
            $schema = $this->modifySchema($schema, $table_name);
            $migration_name = 'create_' . $table_name . '_table';

            $this->makeMigration($table_name, $migration_name, $migration_path, $schema);
        }


    }

    public function makeMigration($table_name, $migration_name, $migration_path, $schema)
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

    public function modifySchema($schema, $table)
    {
        $this->output->writeln('Modifying Schema... Have no Idea what is going on with indexes ');
        $schema = str_replace('pos_', '', $schema);
        $schema = str_replace('manufacturer_brand', 'brand', $schema);
        $schema = str_replace('purchase_order', 'po', $schema);
        $schema = str_replace("\$table->unique(['promotion_id','product_id','product_category_id'],'promotion_id');", '', $schema);
        $schema = str_replace("\$table->unique(['local_tax_jurisdiction_id','state_tax_jurisdiction_id'],'local_tax_jurisdiction_id');", '', $schema);

        $schema = str_replace("\$table->unique(['state_regular_sales_tax_rate_id','state_exemption_sales_tax_rate_id'],'state_regular_sales_tax_rate_id');", '', $schema);
        $schema = str_replace("\$table->unique(['sales_invoice_id','customer_payment_id'],'sales_invoice_id');", '', $schema);
        $schema = str_replace("\$table->unique(['sales_invoice_id','promotion_id'],'sales_invoice_id');", '', $schema);
        $schema = str_replace("\$table->unique(['default_gift_card_account_id','default_store_credit_account_id','default_prepay_account_id'],'default_gift_card_account_id');", '', $schema);
        $schema = str_replace("\$table->unique(['first_name','last_name'],'first_name');", '', $schema);
        $schema = str_replace("\$table->unique(['product_image_id','product_id'],'product_image_id');", '', $schema);


        //this one fucks up pos_sales_tax_category_id and purchase order categry id $schema = str_replace('category_id', 'product_category_id', $schema);
        //$table = rtrim($table, "s");
        //$schema = str_replace($table .'_', '', $schema);
        return $schema;

    }




    public static function getMigrationName($name)
    {
        return 'create_' . $name . '_table';

    }

    protected function getMigrationFileName($migration_name)
    {
        return date('Y_m_d_His') . '_' . $migration_name . '.php';
    }

    protected function compileMigrationStub($table_name, $migration_name, $schema)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/migration.stub');
        $name_parser = new NameParser();
        //$table_name = $name_parser->getTableNameFromMigrationName($migration_name);
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