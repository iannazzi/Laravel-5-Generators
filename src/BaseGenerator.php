<?php

namespace Iannazzi\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class BaseGenerator
{
    protected $files;
    protected $command;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files)
    {

        $this->files = $files;
        $this->command = new Command();
    }

//    public function __construct()
//    {
//        $this->files = new Filesystem();
//    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }





}