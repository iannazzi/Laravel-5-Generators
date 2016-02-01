<?php
namespace Iannazzi\Generators\Migrations;



interface ModelGeneratorInterface
{
    protected function getModelName();
    protected function getModelPath();
}