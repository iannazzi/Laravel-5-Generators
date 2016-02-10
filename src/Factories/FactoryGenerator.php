<?php

namespace Iannazzi\Generators\Factories;

use App\Classes\Library\ArrayOperator;
use File;
use Iannazzi\Generators\BaseGenerator;
use Iannazzi\Generators\Migrations\SchemaGenerator;

class FactoryGenerator extends BaseGenerator
{
   public function makeFactoriesFromExistingDatabase($dbc, $factory_path, $namespace, $map)
   {

       foreach ($map['tables'] as $table => $table_map)
       {
           if (isset($map['tables'][ $table ]['make_factory']))
           {
               continue;
           }

           //skip pivot tables....
           $table_type = $map['tables'][ $table ]['type'];
           if($table_type == 'pivot') continue;

           $model_name = $this->getModelName($map['tables'][ $table ]['new_name']);
           $file_name = $factory_path . '/'.$model_name . 'Factory.php';


           $this->deleteFile($file_name);

           $this->output->writeln('Creating Factory File  ' . $model_name . 'Factory');

           $fields = $this->getFields($dbc,$table, $map);

           $contents['factory'] = $this->getFactory($fields);

//
           $this->makeFactory($model_name, $factory_path, $namespace, $contents);
       }

   }
    protected function getFactory($fields)
    {
        $return_array = [];
        foreach($fields as $field)
        {
            if(is_array($field['field']))
            {
                continue;
            }
            if($field['type'] == 'increments')
            {
                continue;
            }
            $return_array[] = '\'' . $field['field'] .'\' => $faker->name';
        }
        return $return_array;

    }

    protected function makeFactory($model_name, $factory_path, $namespace, $contents)
    {
        $factory_filename = $factory_path . '/' . $model_name . 'Factory.php';
        if ($this->files->exists($factory_filename))
        {
            dd($factory_filename . ' already exists!');
        }
        $this->makeDirectory($factory_filename);

        $compileModelStub = $this->compileFactoryStub($factory_path, $model_name, $namespace, $contents);


        $this->files->put($factory_filename, $compileModelStub);

        $this->output->writeln($model_name . ' Factory created successfully.');
    }

    protected function compileFactoryStub($factory_path, $model_name, $namespace, $contents)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/factory.stub');
        $this
            ->replaceNamespace($stub, $namespace, $model_name)
            ->replaceFactory($stub, $contents['factory'])
        ;

        return $stub;
    }

    protected function replaceNamespace(&$stub, $namespace, $model_name)
    {
        $stub = str_replace('{{namespace_model}}', $namespace . '\\' . $model_name, $stub);
        return $this;
    }

    protected function replaceFactory(&$stub, $factory)
    {

        $factory = ArrayOperator::exportObjectAsString($factory);
        $stub = str_replace('{{factory}}',$factory, $stub);

        return $this;
    }
}