<?php

use GuzzleHttp\ClientInterface;
use Pdo\Sqlite;

return [
    ClientInterface::class => DI\get(\GuzzleHttp\Client::class),
    \Pdo\Sqlite::class => new Sqlite('sqlite:/app/database.sqlite'),
];