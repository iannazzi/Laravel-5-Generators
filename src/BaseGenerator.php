<?php

namespace Iannazzi\Generators;


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
    }

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