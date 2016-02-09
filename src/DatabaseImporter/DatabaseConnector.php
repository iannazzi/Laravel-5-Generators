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
            'BLUEHOST_POS_TEST' => [
                'driver' => 'mysql',
                'host' => env('BLUEHOST_POS_TEST_DB_HOST'),
                'database' => env('BLUEHOST_POS_TEST_DB_DATABASE'),
                'username' => env('BLUEHOST_POS_TEST_DB_USERNAME'),
                'password' => env('BLUEHOST_POS_TEST_DB_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'port' => '3306'
            ],
            'BLUEHOST_POS01' => [
                'driver' => 'mysql',
                'driver' => 'mysql',
                'host' => env('BLUEHOST_POS01_DB_HOST'),
                'database' => env('BLUEHOST_POS01_DB_DATABASE'),
                'username' => env('BLUEHOST_POS01_DB_USERNAME'),
                'password' => env('BLUEHOST_POS01_DB_PASSWORD'),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'port' => '3306'
            ],
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
            'BLUEHOST_POS01' => [
                'driver' => 'mysql',
                'driver' => 'mysql',
                'host' => env('BLUEHOST_POS01_DB_HOST'),
                'database' => env('BLUEHOST_POS01_DB_DATABASE'),
                'username' => env('BLUEHOST_POS01_DB_USERNAME'),
                'password' => env('BLUEHOST_POS01_DB_PASSWORD'),
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