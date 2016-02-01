<?php

namespace Iannazzi\Generators\Migrations;

use Iannazzi\Generators\Migrations\BaseGenerator;

class ModelGenerator extends BaseGenerator
{
    /**
     * Generate an Eloquent model, if the user wishes.
     */
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