<?php

declare(strict_types=1);

namespace Test\infrastructure\Invoice;

use App\infrastructure\Invoice\InvoiceSQLiteRepository;
use Test\ContainerAwareTestCase;

final class InvoiceSQLiteRepositoryTest extends ContainerAwareTestCase
{
    public function testCreate(): void
    {
        /** @var InvoiceSQLiteRepository $repository */
        $repository = $this->getContainer()->get(InvoiceSQLiteRepository::class);

        $invoice = [
            'amount' => 255.99,
            'exchange_rate' => 25.15,
            'currency' => 'CZK',
            'issued_on' => '2025-03-25',
            'vat' => 23.0,
            'created_at' => '2025-03-29 15:34:35',
        ];

        $created_invoice = $repository->create($invoice);

        self::assertNotNull($created_invoice['id']);
        // We expect it to return invoice as we sent it
        self::assertEquals($created_invoice, [...$invoice, 'id' => $created_invoice['id']]);

        $persisted_invoice = $repository->findById((int) $created_invoice['id']);

        // We also expect to find that saved invoice with same state
        self::assertEquals($persisted_invoice, $created_invoice);
    }
}