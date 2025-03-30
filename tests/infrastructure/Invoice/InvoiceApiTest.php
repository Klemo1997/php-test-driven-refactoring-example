<?php

declare(strict_types=1);

namespace Test\infrastructure\Invoice;

use App\infrastructure\Invoice\InvoiceController;
use App\infrastructure\Invoice\InvoiceSQLiteRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use Test\ContainerAwareTestCase;

#[CoversClass(InvoiceController::class)]
final class InvoiceApiTest extends ContainerAwareTestCase
{
    public function testAdd(): void
    {
        $invoice_create_request = [
            'amount' => 12.50,
            'currency' => 'CZK',
            'issued_on' => '2025-03-25',
        ];

        $response = $this->postJson('/invoice', $invoice_create_request)
            ->assertCreated();

        $response_invoice = json_decode(
            (string) $response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $persisted_invoice = $this->getInvoiceRepository()->findById((int) $response_invoice['id']);

        self::assertEquals($persisted_invoice, $response_invoice);
    }

    public function getInvoiceRepository(): InvoiceSQLiteRepository
    {
        return $this->getContainer()->get(InvoiceSQLiteRepository::class);
    }
}