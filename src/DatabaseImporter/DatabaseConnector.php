<?php
namespace Iannazzi\Generators\DatabaseImporter;

class DatabaseConnector
{

    public function __construct()
    {
        //$this->addConnections();
    }

    public static function addConnections()
    {
        $new_connections = [
            'POS' => [
                'driver'    => 'mysql',
                'host'      => env('DB_HOST'),
                'database'  => 'POS',
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
                'strict'    => false,
                'port' => '3306'
            ],
            'POS_PRODUCTION' => [
                'driver' => 'mysql',
                'host' => env('POS_PRODUCTION_DB_HOST'),
                'database' => env('POS_PRODUCTION_DB_DATABASE'),
                'username' => env('POS_PRODUCTION_DB_USERNAME'),
                'password' => env('POS_PRODUCTION_DB_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'port' => '3306'
            ],
        ];
        $db_connections = \Config::get('database.connections');
        foreach ($new_connections as $key => $value) {
            $db_connections[$key] = $value;
        }

        \Config::set('database.connections', $db_connections);
        return \Config::get('database.connections');
    }
}