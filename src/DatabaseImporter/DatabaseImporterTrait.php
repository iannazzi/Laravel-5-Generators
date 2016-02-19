<?php
namespace Iannazzi\Generators\DatabaseImporter;

use App\Classes\Library\ArrayOperator;
use Symfony\Component\Console\Output\ConsoleOutput;

trait DatabaseImporterTrait
{
    public static function console($msg)
    {
        $out = new ConsoleOutput();
        $out->writeln($msg);
    }

    public function out($msg)
    {
        $this->console($msg);
    }

    public function output($msg)
    {
        $this->console($msg);
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
        $maps[] = DatabaseMigrationMap::getCraigloriousTableMap();
        $maps[] = DatabaseMigrationMap::getTenantTableMap();

        foreach ($maps as $map)
        {
            if (array_key_exists($table, $map['tables'])) return $map;
        }

        return false;
    }

    public static function runFunctionOnColumns($data)
    {
        $new_array = [];
        for ($i = 0; $i < sizeof($data); $i ++)
        {
            foreach ($data[$i] as $key => $value)
            {
                $key = str_replace('pos_', '', $key);
                $key = str_replace('manufacturer_brand', 'brand', $key);
                $new_array[$i][ $key ] = $value;
            }
        }
        return $new_array;
    }
}