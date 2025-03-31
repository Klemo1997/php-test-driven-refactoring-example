<?php

use App\domain\Invoice\ExchangeRate\ExchangeRateProvider;
use App\domain\Invoice\InvoiceRepository;
use App\infrastructure\Date\SystemClock;
use App\infrastructure\Invoice\ExchangeRate\NBSExchangeRateProvider;
use App\infrastructure\Invoice\InvoiceSQLiteRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Pdo\Sqlite;
use Psr\Clock\ClockInterface;

return [
    ClientInterface::class => DI\get(Client::class),
    Sqlite::class => new Sqlite('sqlite:/app/database.sqlite'),
    ClockInterface::class => DI\get(SystemClock::class),
    ExchangeRateProvider::class => DI\get(NBSExchangeRateProvider::class),
    InvoiceRepository::class => DI\get(InvoiceSQLiteRepository::class),
];