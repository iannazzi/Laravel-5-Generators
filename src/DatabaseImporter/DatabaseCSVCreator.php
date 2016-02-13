<?php

namespace Iannazzi\Generators\DatabaseImporter;


use App\Classes\File\CIFile;
use App\Classes\Library\ArrayOperator;
use DB;
use Iannazzi\Generators\BaseGenerator;

class DatabaseCSVCreator
{
    use DatabaseImporterTrait;

    public static function createStartupSCVFile($dbc, $table)
    {
        DatabaseConnector::addConnections();


        $path = base_path() . '/database/seeds/csv_startup_data/';
        $path = base_path() . '/database/exports/csv';

        $file = new CIFile();

        $sql['pos_categories'] = 'Select pos_category_id, name, parent, pos_sales_tax_category_id, default_product_priority, active From pos_categories';
        $sql['pos_user_groups'] = 'Select * From pos_user_groups';
        $sql['pos_chart_of_accounts'] = 'Select * From pos_chart_of_accounts';
        $sql['pos_discounts'] = 'Select * From pos_discounts';
        $sql['pos_settings'] = 'Select * From pos_settings';

        $sql['pos_product_options'] = 'SELECT * from pos_product_options';
        $sql['pos_product_attributes'] = 'SELECT * from pos_product_attributes';


        if ( ! isset($sql[ $table ]))
        {
            self::console($table . ' is not found in the export options');

            return;
        }

        $data = DB::connection($dbc)->select($sql[ $table ]);
        $data = self::mapData($table, $data);

        $filename = self::makeFilename($table, $path);
        $file->arrayToCSVFile($filename, $data, ';', false, true);


    }

    public static function makeFilename($table, $path)
    {
        $map = self::chooseMap($table);
        return $path . "/" . $map['tables'][$table]['new_name'] . '.csv';
    }

    public static function mapData($table, $data)
    {
        $map = self::chooseMap($table);
        if ($map)
        {
            $columns = self::getColumns($map['tables'][ $table ], 'drop_columns');
            $data = ArrayOperator::dropColumns($data, $columns);
            $columns = self::getColumns($map['tables'][ $table ], 'rename_columns');
            $data = ArrayOperator::renameColumns($data, $columns);

        }

        return $data;
    }

    public static function getColumns($map, $column_name)
    {
        return (isset($map[ $column_name ])) ? $map[ $column_name ] : [];
    }

    public static function chooseMap($table)
    {
        //not sure which map so choose it here...
        $maps[] = DatabaseMigrationMap::getCraigloriousTablesFromBluehost();
        $maps[] = DatabaseMigrationMap::getTenantTablesFromBluehost();

        foreach ($maps as $map)
        {
            if (array_key_exists($table, $map['tables'])) return $map;
        }

        return false;
    }

}