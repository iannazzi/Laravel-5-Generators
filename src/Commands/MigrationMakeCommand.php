<?php

namespace Iannazzi\Generators\Commands;

use Iannazzi\Generators\Migrations\MigrationGenerator;
use Iannazzi\Generators\Migrations\NameParser;
use Iannazzi\Generators\Migrations\SchemaParser;
use Iannazzi\Generators\Migrations\SyntaxBuilder;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Iannazzi\Generators\Migrations\MigrationGeneratorInterface;

class MigrationMakeCommand extends Command implements MigrationGeneratorInterface
{
    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:migration:schema';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration class and apply schema at the same time';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Meta information for the requested migration.
     *
     * @var array
     */
    protected $meta;
    protected $table_name, $action;

    /**
     * @var Composer
     */
    private $composer;



    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files)

    {
        parent::__construct();

        $this->files = $files;
        $this->composer = app()['composer'];

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //php artisan make:migration:schema create_users_table --schema="username:string, email:string:unique"

        $this->meta = (new NameParser)->parse($this->argument('name'));

        $this->table_name = $this->meta['table'];
        $this->action = $this->meta['action'];

        $migration_name = $this->argument('name');
        $migration_path = $this->getMigrationPath();

        $schema = $this->getSchemaOptions();
        $migration_generator = new MigrationGenerator();
        $migration_generator->makeMigrationFromCommand($migration_name, $migration_path, $schema);

        $this->composer->dumpAutoloads();
        //$model_name = $this->modelGenerator->createModelName($this->table_name);
        //$this->modelGenerator->makeEmptyModel($model_name);
    }

    /**
     * Get the path to where we should store the migration.
     *
     * @param  string $name
     * @return string
     */
    public function getMigrationPath()
    {
        return base_path() . '/database/migrations';
    }

    /**
     * @return array|string
     */
    public function getSchemaOptions()
    {
        $schema = $this->option('schema');

        if ($schema)
        {
            $schema = (new SchemaParser)->parseSchema($schema);
        }

        $schema = (new SyntaxBuilder)->create($schema, $this->meta);
        dd($schema);
        return $schema;
    }

    /**
     * Get the class name for the Eloquent model generator.
     *
     * @return string
     */
    protected function getModelName()
    {
        return $this->meta['table'];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the migration'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
            ['model', null, InputOption::VALUE_OPTIONAL, 'Want a model for this table?', true],
        ];
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getModelPath($name)
    {
        $name = str_replace($this->getAppNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }



}
