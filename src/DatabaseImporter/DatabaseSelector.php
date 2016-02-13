<?php
namespace Iannazzi\Generators\DatabaseImporter;

use App\Classes\Database\DatabaseManagerTrait;
use Config;
use DB;

Class DatabaseSelector
{
	use DatabaseManagerTrait;

	public static function getTables($dbc)
	{
		//$sql = "show tables";
		//$sql = "select * from information_schema.tables";
		$database_name = Config::get('database.connections.'.$dbc.'.database');
		$sql = "select table_name from information_schema.tables where table_schema='".$database_name."'";

		$query =  DB::connection($dbc)->select($sql);
		$tables = [];
		foreach($query as $table)		
		{
			$table = $table['table_name'];
			//$this->output->writeln($table);
			$tables[] = $table;
		}
		return $tables;
	}
}