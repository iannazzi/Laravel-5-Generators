<?php

namespace Iannazzi\Generators\Migrations;

use File;
use Iannazzi\Generators\Migrations\BaseGenerator;

class ModelGenerator extends BaseGenerator
{
   protected function makeModelsFromExistingDatabase($connection, $model_path, $map)
   {
       $this->removeModels($model_path, $map);
   }
    protected function getModelName($table_name)
    {
        return ucwords(str_singular(camel_case($table_name)));
    }
    public function removeModels($path, $map)
    {
        $files = File::files($path);
        foreach($files as $file)
        {
            foreach($map['tables'] as $original_name => $new_array )
            {
                $file_name = getModelName($new_array['new_name']) .'.php';
                if(strpos(basename($file), $file_name) !== false)
                {
                    $this->output->writeln('delete_file' . basename($file));
                    File::delete($file);
                }

            }
        }
    }
    protected function makeModel($model_name)
    {
        $modelPath = $this->getModelPath($model_name);

        //this is a start.... but we need to add a ton to this one....

        if ($this->option('model') && !$this->files->exists($modelPath)) {
            $this->call('make:model', [
                'name' => $this->getModelName()
            ]);
        }
    }
    protected function makeEmptyModel($model_name)
    {
        $modelPath = $this->getModelPath($model_name);


        if ($this->option('model') && !$this->files->exists($modelPath)) {
            $this->call('make:model', [
                'name' => $this->getModelName()
            ]);
        }
    }
    protected function createModelName($table_name)
    {
        return ucwords(str_singular(camel_case($model_name)));
    }
}