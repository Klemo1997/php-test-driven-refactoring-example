<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice\ExchangeRate;

use App\domain\Invoice\ExchangeRate\ExchangeRateProvider;
use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use Fig\Http\Message\RequestMethodInterface;
use GuzzleHttp\ClientInterface;

final readonly class NBSExchangeRateProvider implements ExchangeRateProvider
{
    private const NBS_BASE_URL = 'https://nbs.sk/export/sk/exchange-rate/%s/csv';

    public function __construct(private ClientInterface $client) {
    }

    public function fetch(string $source_currency, string $target_currency, \DateTimeImmutable $date): float
    {
        if ($source_currency !== 'EUR') {
            throw new UnableToFetchExchangeRateException('Invalid source currency');
        }

        $nbs_url = sprintf(self::NBS_BASE_URL, $date->format('Y-m-d'));

        $nbs_response = $this->client->request(RequestMethodInterface::METHOD_GET, $nbs_url);
        $exchange_rates_csv = (string) $nbs_response->getBody();
        $exchange_rates_map = $this->exchangeRatesFromCSV($exchange_rates_csv);

        return $exchange_rates_map[$target_currency]
            ?? throw new UnableToFetchExchangeRateException('Invalid target currency');
    }

    /**
     * @param string $exchange_rates_csv
     * @return array<string, float|null>
     * @throws UnableToFetchExchangeRateException
     */
    private function exchangeRatesFromCSV(string $exchange_rates_csv): array
    {
        $rows = array_map(
            static fn($row) => str_getcsv($row, ';', escape: "\\"),
            explode(PHP_EOL, $exchange_rates_csv),
        );

        if (count($rows) < 2) {
            throw new UnableToFetchExchangeRateException('Invalid response from NBS');
        }

        [$currencies, $exchange_rates] = $rows;

        if (!is_array($currencies) || !is_array($exchange_rates) || count($currencies) !== count($exchange_rates)) {
            throw new UnableToFetchExchangeRateException('Invalid response from NBS');
        }

        // Shift out the redundant date in first indices
        array_shift($currencies);
        array_shift($exchange_rates);

        /** @var array<string, float> $exchange_rates_map */
        $exchange_rates_map = [];

        foreach ($currencies as $index => $currency) {
            $exchange_rate = filter_var(
                str_replace(' ', '', str_replace(',', '.', $exchange_rates[$index])),
                FILTER_VALIDATE_FLOAT,
                FILTER_NULL_ON_FAILURE,
            );
            $exchange_rates_map[$currency] = $exchange_rate;
        }
        return $exchange_rates_map;
    }
}