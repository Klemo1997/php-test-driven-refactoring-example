<?php

declare(strict_types=1);

namespace Test\stubs\Invoice\ExchangeRate;

use App\domain\Invoice\ExchangeRate\ExchangeRateProvider;use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;

final class FakeExchangeRateProvider implements ExchangeRateProvider
{
    /** @var array<string, float> */
    private array $data = [];

    public function addExchangeRate(string $source_currency, string $target_currency, float $exchange_rate): void
    {
        $this->data[$source_currency . '|' . $target_currency] = $exchange_rate;
    }

    public function fetch(string $source_currency, string $target_currency, \DateTimeImmutable $date): float
    {
        return $this->data[$source_currency . '|' . $target_currency]
            ?? throw new UnableToFetchExchangeRateException();
    }
}