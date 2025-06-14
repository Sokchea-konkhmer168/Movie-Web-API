<?php
// Database configuration for MySQL
return [
    'default' => 'mysql',
    
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'movie_ticket_booking',
            'username' => 'root',
            'password' => '', // Add your MySQL password here
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],
];
?>
