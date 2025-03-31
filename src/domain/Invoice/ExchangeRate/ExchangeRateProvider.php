<?php

declare(strict_types=1);

namespace App\domain\Invoice\ExchangeRate;

interface ExchangeRateProvider
{
    public function fetch(string $sourceCurrency, string $targetCurrency, \DateTimeImmutable $date): float;
}