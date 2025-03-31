<?php

declare(strict_types=1);

use Pdo\Sqlite;
use Psr\Clock\ClockInterface;
use Test\stubs\Date\FakeClock;

return [
    Sqlite::class => new Sqlite('sqlite:/app/test.sqlite'),
    ClockInterface::class => DI\get(FakeClock::class),
];