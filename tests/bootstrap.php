<?php

declare(strict_types=1);

$_ENV['APP_ENV'] ??= 'test';

shell_exec('sh ' . __DIR__ . '/../scripts/migrate.sh test.sqlite');