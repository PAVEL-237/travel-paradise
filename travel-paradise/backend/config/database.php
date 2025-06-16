<?php

return [
    'default' => 'pgsql',
    
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'database' => 'travel_paradise',
            'username' => 'postgres',
            'password' => 'pavel',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
]; 