<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class InvoiceController
{
    public function __construct(private InvoiceSQLiteRepository $invoiceRepository) {
    }

    public function addInvoice(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $invoice = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $nbs_url = sprintf('https://nbs.sk/export/sk/exchange-rate/%s/csv', $invoice['issued_on']);

        $http_client = new \GuzzleHttp\Client();

        $nbs_response = $http_client->get($nbs_url);
        $exchange_rates_csv = (string) $nbs_response->getBody();

        $exchange_rates_map = $this->exchangeRatesFromCSV($exchange_rates_csv);

        $invoice['exchange_rate'] = $exchange_rates_map[$invoice['currency']];
        $invoice['vat'] = 23.0;
        $invoice['created_at'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

        // Magic happens inside, that sets id to reference :-(
        $this->invoiceRepository->create($invoice);

        return $response
            ->withStatus(StatusCodeInterface::STATUS_CREATED)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new HttpFactory()->createStream(json_encode($invoice, JSON_THROW_ON_ERROR)));
    }

    /**
     * @param string $exchange_rates_csv
     * @return array<string, float|null>
     */
    private function exchangeRatesFromCSV(string $exchange_rates_csv): array
    {
        [$currencies, $exchange_rates] = array_map(
            static fn($row) => str_getcsv($row, ';', escape: "\\"),
            explode(PHP_EOL, $exchange_rates_csv),
        );

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