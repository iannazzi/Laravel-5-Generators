<?php

namespace Iannazzi\Generators\DatabaseImporter;


class ArrayOperator
{
    public static function dropColumns($data, $columns)
    {
        $new_array = [];
        for($i=0;$i<sizeof($data);$i++)
        {
            foreach ($data[$i] as $key=> $value)
            {
                if( !in_array($key, $columns))
                {
                    $new_array[$i][$key] = $value;
                }
            }
        }
        return $new_array;
    }
    public function renameColumns($data, $rename_column_map)
    {
        $new_array = [];
        for($row=0;$row<sizeof($data);$row++)
        {
            $new_array[$row] = $this->renameRowColumns($data[$row], $rename_column_map);
        }
        return $new_array;
    }

    public function renameRowColumns($row, $rename_column_map)
    {
        $new_array = [];
        foreach ($row as $field => $value)
        {
            $new_array[$this->renameField($field, $rename_column_map)] = $value;
        }
        return $new_array;
    }
    public function renameField($field, $rename_column_map)
    {


        if(array_key_exists($field, $rename_column_map))
        {

            return $rename_column_map[$field];
        }
        return $field;
    }
}