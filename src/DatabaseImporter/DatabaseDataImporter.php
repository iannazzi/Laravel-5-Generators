<?php

namespace Iannazzi\Generators\DatabaseImporter;


use App\Classes\Library\ArrayOperator;
use App\Models\Craiglorious\System;
use App\Models\Tenant\Address;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Vendor;
use DB;
use Schema;

class DatabaseDataImporter
{
    use DatabaseImporterTrait;
    protected $test;
    protected $databaseDestroyer;
    protected $source_connection;

    public function __construct($source_connection, $test)
    {

        $this->test = $test;
        $this->source_connection = $source_connection;
        $this->databaseDestroyer = new DatabaseDestroyer();
    }
    //my first hope was simply copying the database... that is not going to happen

    public static function importVendors($dbc)
    {
        DatabaseConnector::addConnections();
        self::importInventoryVendors($dbc);
        self::importExpenseVendors($dbc);





    }

    private static function importInventoryVendors($dbc)
    {
        $sql = "Select pos_accounts.* from pos_accounts LEFT JOIN pos_account_type using (pos_account_type_id)
                                where pos_account_type.account_type_name = 'Inventory Account'";
        self::addVendors($dbc, $sql, 'Inventory');
    }

    private static function importExpenseVendors($dbc)
    {
        $sql = "Select pos_accounts.* from pos_accounts LEFT JOIN pos_account_type using (pos_account_type_id)
                                where pos_account_type.account_type_name = 'Expense Account'";
        self::addVendors($dbc, $sql, 'Expense');
    }

    private static function addVendors($dbc, $sql, $type)
    {
        $rows =  DB::connection($dbc)->select($sql);
        foreach($rows as $row)
        {
            $address =  Address::create([
                'address1' => $row->address1,
                'address2' => $row->address2,
                'city' => $row->city,
                'state' => $row->state,
                'zip' => $row->zip,
                'country' => $row->country,
                'phone' => $row->phone,
                'fax' => $row->fax,
                'comments' => 'Contact: ' . $row->primary_contact .PHP_EOL. 'Emails: ' . $row->email,
                'active' => 1
            ]);
            //going to loose the contact....
            $vendor = Vendor::create([
                'contact_id' => 0,
                'billing_address_id' => $address->id,
                'shipping_address_id' => 0,
                'type' => $type,
                'name' => $row->company,
                'account_number' => bcrypt(craigsDecryption($row->account_number)),
                'active' => 1
            ]);


        }
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


        $craiglorious_tables_map = DatabaseMigrationMap::getCraigloriousTableMap();
        $tenant_tables_map = DatabaseMigrationMap::getTenantTableMap();

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
        $this->databaseDestroyer->emptyTable($dest_connection, $dest_table);
        if ( ! isset($map['tables'][ $source_table ]['import_data']))
        {
            $num_chunk_records = 1000;
            $me = $this;
            $msg = 'Seeding MAX ' . $num_chunk_records . ' from: ' . $source_table . ' to table ' . $dest_table;
            $this->console('OUTPUT: ' . $msg);
            DB::connection($souce_connection)->table($source_table)->chunk($num_chunk_records, function ($data_chunk)
            use ($me, $dest_connection, $source_table, $dest_table, $map, $num_chunk_records)
            {
                $data_chunk = $this->preEntryMap($data_chunk, $source_table, $dest_table, $map);
                $this->loadDataIntoTable($dest_connection, $dest_table, $data_chunk);
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

    public function loadDataIntoTable($dbc, $table, $data)
    {
        $this->console('Loading data to table ' . $table);
        foreach ($data as $row)
        {
            $this->insertRow($dbc, $table, $row);
        }
    }

    public function insertRow($dbc, $table, $row)
    {
        DB::Connection($dbc)->table($table)->insert($row);
        //other than this we would need to get the models....

    }

    public function insertField($dbc, $table, $field, $value)
    {
        if (Schema::Connection($dbc)->hasColumn($table, $field))
        {
            dd([$field => $value]);
            DB::Connection($dbc)->table($table)->insert([$field => $value]);

            return true;
        }
        dd('insertField: ' . $field);

    }


}