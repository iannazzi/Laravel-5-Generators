<?php

namespace Iannazzi\Generators;


use File;
use Iannazzi\Generators\DatabaseImporter\DatabaseConnector;
use Iannazzi\Generators\Migrations\SchemaGenerator;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Filesystem\Filesystem;

class BaseGenerator
{
    protected $files;
    protected $output;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct()
    {
        $this->files = new Filesystem();
        $this->output = new ConsoleOutput();
        DatabaseConnector::addConnections();
    }

    protected static function getModelName($table_name)
    {
        return ucwords(str_singular(camel_case($table_name)));
    }

    public function getNamespace($app_name, $model_path)
    {
        $namespace = str_replace(app_path(), '', $model_path);
        $namespace = $app_name . str_replace('/', '\\', $namespace);

        return $namespace;
    }

    protected function getFields($dbc, $table, $map)
    {
        $schemaGenerator = new SchemaGenerator($dbc, true, true);
        $fields = $schemaGenerator->getFields($table);
        $fields = $this->dropFields($table, $fields, $map);
        $fields = $this->renameFields($table, $fields, $map);
        $fields = $this->runFunctionOnFields($fields);
        return $fields;
    }
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
    public function removeFiles($path, $map, $nameFunction)
    {
        $this->output->writeln('Statrting File Removal');
        $files = File::files($path);
        foreach($files as $file)
        {
            $this->removeFile($file, $map, $nameFunction);
        }
        $this->output->writeln('File Removal Complete');
    }
    public function removeFile($file, $map, $nameFunction)
    {
        foreach($map['tables'] as $original_name => $new_array )
        {
            $file_name = $nameFunction($new_array['new_name']);
            if(strpos(basename($file), $file_name) !== false)
            {
               $this->deleteFile($file);
            }
        }
    }
    public function deleteFile($file_name)
    {
        $this->output->writeln('Deleting File ' . basename($file_name));
        File::delete($file_name);
    }
    public function dropFields($table, $fields, $map)
    {
        if ( ! isset($map['tables'][ $table ]['drop_columns']))
        {
            return $fields;
        }
        $mapped_fields = [];
        foreach ($fields as $field)
        {
            if ( ! in_array($field['field'], $map['tables'][ $table ]['drop_columns']))
            {
                $mapped_fields[] = $field;
            }
        }

        return $mapped_fields;
    }

    public function renameFields($table, $fields, $map)
    {

        if ( ! isset($map['tables'][ $table ]['rename_columns']))
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
        if (is_array($field['field']))
        {

            for ($i = 0; $i < sizeof($field['field']); $i ++)
            {
                if (array_key_exists($field['field'][ $i ], $map['tables'][ $table ]['rename_columns']))
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

    public function runFunctionOnFields($fields)
    {
        $new_array = [];
        foreach ($fields as $field)
        {
            $field['field'] = str_replace('pos_', '', $field['field']);
            $field['field'] = str_replace('manufacturer_brand', 'brand', $field['field']);
            $new_array[] = $field;
        }

        return $new_array;
    }



}