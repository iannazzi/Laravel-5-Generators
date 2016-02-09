<?php
namespace Iannazzi\Generators\DatabaseImporter;

use Symfony\Component\Console\Output\ConsoleOutput;

trait DatabaseImporterTrait
{
    public function console($msg)
    {
        $out = new ConsoleOutput();
        $out->writeln($msg);
    }


}