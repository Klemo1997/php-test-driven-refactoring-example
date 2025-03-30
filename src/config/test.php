<?php

declare(strict_types=1);

use Pdo\Sqlite;

return [
    \Pdo\Sqlite::class => new Sqlite('sqlite:/app/test.sqlite'),
];