<?php

declare(strict_types=1);

namespace App\domain\Invoice;

use App\domain\Invoice\ExchangeRate\ExchangeRateProvider;
use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use App\domain\Invoice\Vat\UnableToFetchVatException;
use App\domain\Invoice\Vat\VatProvider;
use Psr\Clock\ClockInterface;

final readonly class AddInvoiceUseCase
{
    public function __construct(
        private InvoiceRepository $invoiceRepository,
        private ExchangeRateProvider $exchangeRateProvider,
        private ClockInterface $clock,
        private VatProvider $vatProvider,
    ) {
    }

    /**
     * @throws UnableToFetchExchangeRateException
     * @throws UnableToFetchVatException
     */
    public function execute(array $invoice): array
    {
        $issued_on = new \DateTimeImmutable($invoice['issued_on']);
        $invoice['exchange_rate'] = $this->exchangeRateProvider->fetch(
            'EUR',
            $invoice['currency'],
            $issued_on,
        );

        $invoice['vat'] = $this->vatProvider->fetch($issued_on);
        $invoice['created_at'] = $this->clock->now()
            ->format('Y-m-d H:i:s');

        return $this->invoiceRepository->create($invoice);
    }
}