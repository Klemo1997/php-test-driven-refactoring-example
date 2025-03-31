<?php

declare(strict_types=1);

namespace Test\domain\Invoice;

use App\domain\Invoice\AddInvoiceUseCase;
use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use App\domain\Invoice\InvoiceRepository;
use App\domain\Invoice\Vat\SlovakVatProvider;
use PHPUnit\Framework\TestCase;
use Test\stubs\Date\FakeClock;
use Test\stubs\Invoice\ExchangeRate\FakeExchangeRateProvider;

final class AddInvoiceUseCaseTest extends TestCase
{
    private const CREATED_INVOICE_ID = 1;

    private const SOURCE_CURRENCY = 'EUR';
    private const TARGET_CURRENCY = 'BTC';
    private const EXCHANGE_RATE = 76182.64;
    private const UNSUPPORTED_TARGET_CURRENCY = 'ETH';

    public function testExecute(): void
    {
        $use_case = $this->getUseCase();

        $invoice = [
            'amount' => 12.5,
            'currency' => self::TARGET_CURRENCY,
            'issued_on' => '2025-03-31 19:00:00',
        ];

        $expected_invoice = [
            'id' => self::CREATED_INVOICE_ID,
            'amount' => 12.5,
            'vat' => 23.0,
            'currency' => self::TARGET_CURRENCY,
            'issued_on' => '2025-03-31 19:00:00',
            'exchange_rate' => self::EXCHANGE_RATE,
            'created_at' => FakeClock::FAKE_TIME,
        ];

        self::assertEquals($expected_invoice, $use_case->execute($invoice));
    }

    public function testExecuteWithUnsupportedTargetCurrency(): void
    {
        $this->expectException(UnableToFetchExchangeRateException::class);

        $invoice = [
            'amount' => 12.5,
            'currency' => self::UNSUPPORTED_TARGET_CURRENCY,
            'issued_on' => '2025-03-31 19:00:00',
        ];

        $this->getUseCase()->execute($invoice);
    }


    private function getUseCase(): AddInvoiceUseCase
    {
        $fake_exchange_rate_provider = new FakeExchangeRateProvider();
        $fake_exchange_rate_provider->addExchangeRate(
            self::SOURCE_CURRENCY,
            self::TARGET_CURRENCY,
            self::EXCHANGE_RATE,
        );

        return new AddInvoiceUseCase(
            $this->getFakeInvoiceRepository(),
            $fake_exchange_rate_provider,
            new FakeClock(),
            new SlovakVatProvider(),
        );
    }

    private function getFakeInvoiceRepository(): InvoiceRepository
    {
        $invoiceRepositoryMock = $this->createMock(InvoiceRepository::class);
        $invoiceRepositoryMock->method('create')
            // Just add predefined id to input
            ->willReturnCallback(function (array $invoice) {
                return [...$invoice, 'id' => self::CREATED_INVOICE_ID];
            });
        return $invoiceRepositoryMock;
    }
}