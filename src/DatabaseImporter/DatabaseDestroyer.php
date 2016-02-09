<?php

namespace Iannazzi\Generators\DatabaseImporter;


use DB;

class DatabaseDestroyer
{
    use DatabaseImporterTrait;
    public static function deleteAllTenantDatabases()
    {
        //the database name prefix can change....currently hardode...
        $prefix = 'cs_';
        $databases = DB::connection('craiglorious')->select('Show databases');
        for ($i = 0; $i < sizeof($databases); $i ++)
        {
            $dbn = $databases[ $i ]['Database'];

            if (strpos($dbn, $prefix) !== false)
            {
                $sql = 'Drop Database ' . $dbn;
                echo 'Deleting ' . $dbn . PHP_EOL;
                DB::connection('craiglorious')->statement($sql);
            }
        }
    }
    public function emptyTable($dbc, $table)
    {
        //only empty on the tenant connection
        $msg = 'Truncation ' . $table . ' On Connection ' . $dbc;
        $this->console($msg);
        DB::connection($dbc)->table($table)->truncate();
        //$delete_q = "Delete From " . $table . " where 1";
        //DB::connection($this->tdbc)->statement($delete_q);
    }
    public function dropAllTables($dbc)
    {
        $tables = DatabaseSelector::getTables($dbc);
        foreach($tables as $table)
        {

            $this->dropTable($dbc, $table);
        }
    }
    public function dropTable($dbc, $table)
    {
        DB::connection($dbc)->statement("Drop table " . $table);


        $msg = 'Dropped table ' . $table . ' On Connection ' . $dbc;
        $this->console($msg);
    }
}