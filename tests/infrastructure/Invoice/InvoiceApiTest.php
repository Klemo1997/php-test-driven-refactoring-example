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
        $invoice = [
            'amount' => 12.50,
            'currency' => 'CZK',
            'issued_on' => '2025-03-25',
        ];

        $response = $this->postJson('/invoice', $invoice)
            ->assertCreated();

        $responseInvoice = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        /** @var InvoiceSQLiteRepository $repository */
        $repository = $this->getContainer()->get(InvoiceSQLiteRepository::class);
        $persistedInvoice = $repository->findById((int) $responseInvoice['id']);

        self::assertEquals($persistedInvoice, $responseInvoice);
    }
}