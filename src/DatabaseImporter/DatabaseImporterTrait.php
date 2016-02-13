<?php
namespace Iannazzi\Generators\DatabaseImporter;

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


}