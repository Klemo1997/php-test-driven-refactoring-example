<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use App\domain\Invoice\ExchangeRate\ExchangeRateProvider;
use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use App\domain\Invoice\InvoiceRepository;
use Psr\Clock\ClockInterface;

final readonly class AddInvoiceUseCase
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private ExchangeRateProvider $exchangeRateProvider,
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