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
        $sql['pos_zip_codes'] = 'Select pos_zip_code_id, zip_code, pos_state_id, state, pos_county_id, county from pos_zip_codes';


        $sql['pos_zip_codes'] = 'Select pos_zip_code_id,zip_code,state,pos_state_id,primary_city, acceptable_cities, county,pos_county_id,timezone,latitude,longitude,country from pos_zip_codes';

        $sql['pos_product_options'] = 'SELECT * from pos_product_options';
        $sql['pos_product_attributes'] = 'SELECT * from pos_product_attributes';
        $sql['pos_chart_of_accounts_required'] = 'SELECT * from pos_chart_of_accounts_required';
        $sql['pos_chart_of_account_types'] = 'SELECT * from pos_chart_of_account_types';


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





}