<?php

declare(strict_types=1);

namespace App\domain\Invoice\ExchangeRate;

interface ExchangeRateProvider
{
    /**
     * @throws UnableToFetchExchangeRateException
     */
    public function fetch(string $source_currency, string $target_currency, \DateTimeImmutable $date): float;
}