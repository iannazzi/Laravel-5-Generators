<?php

namespace Iannazzi\Generators\DatabaseImporter;


use App\Classes\Database\TenantDatabaseConnector;
use DB;

class DatabaseDestroyer
{
    use DatabaseImporterTrait;
    public static function deleteAllTenantDatabases()
    {
        $prefix = TenantDatabaseConnector::GetDBCPrefix();
        $databases = DB::connection('main')->select('Show databases');
        for ($i = 0; $i < sizeof($databases); $i ++)
        {
            //$dbn = $databases[ $i ]['Database'];
            $dbn = $databases[ $i ]->Database;

            if (strpos($dbn, $prefix) !== false)
            {
                $sql = 'Drop Database ' . $dbn;
                echo 'Deleting ' . $dbn . PHP_EOL;
                DB::connection('main')->statement($sql);
            }
        }
        DB::connection('main')->statement('SET FOREIGN_KEY_CHECKS = 1');

    }
    public static function emptyTable($dbc, $table)
    {
        //only empty on the tenant connection
        $msg = 'Truncation ' . $table . ' On Connection ' . $dbc;
        self::console($msg);
        DB::connection($dbc)->table($table)->truncate();
        //$delete_q = "Delete From " . $table . " where 1";
        //DB::connection($this->tdbc)->statement($delete_q);
    }
    public static function dropAllTables($dbc)
    {
        $tables = DatabaseSelector::getTables($dbc);
        foreach($tables as $table)
        {
            self::dropTable($dbc, $table);
        }
    }
    public static function dropTable($dbc, $table)
    {
        DB::connection($dbc)->statement('SET FOREIGN_KEY_CHECKS = 0');

        DB::connection($dbc)->statement("Drop table " . $table);
        $msg = 'Dropped table ' . $table . ' On Connection ' . $dbc;
        self::console($msg);
        DB::connection($dbc)->statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}