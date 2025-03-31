<?php

use App\infrastructure\Date\SystemClock;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Pdo\Sqlite;
use Psr\Clock\ClockInterface;

return [
    ClientInterface::class => DI\get(Client::class),
    Sqlite::class => new Sqlite('sqlite:/app/database.sqlite'),
    ClockInterface::class => DI\get(SystemClock::class),
];