<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use App\infrastructure\Invoice\ExchangeRate\NBSExchangeRateProvider;
use App\infrastructure\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use Psr\Clock\ClockInterface;

final readonly class AddInvoiceUseCase
{
    public function __construct(
        private InvoiceSQLiteRepository $invoiceRepository,
        private NBSExchangeRateProvider $exchangeRateProvider,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws UnableToFetchExchangeRateException
     */
    public function execute(array $invoice): array
    {
        $invoice['exchange_rate'] = $this->exchangeRateProvider->fetch(
            'EUR',
            $invoice['currency'],
            new \DateTimeImmutable($invoice['issued_on']),
        );

        $invoice['vat'] = 23.0;
        $invoice['created_at'] = $this->clock->now()
            ->format('Y-m-d H:i:s');

        return $this->invoiceRepository->create($invoice);
    }
}