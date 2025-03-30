<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use Pdo\Sqlite;

final class InvoiceSQLiteRepository
{
    public function create(array &$invoice): void
    {
        $test = new Sqlite('sqlite:/app/database.sqlite');

        $statement = $test->prepare(<<<SQL
            INSERT INTO invoices(amount, currency, exchange_rate, issued_on, created_at)
            VALUES(:amount, :currency, :exchange_rate, :issued_on, :created_at)
            SQL);

        $isSuccessful = $statement->execute([
            ':amount' => $invoice['amount'],
            ':currency' => $invoice['currency'],
            ':exchange_rate' => $invoice['exchange_rate'],
            ':issued_on' => $invoice['issued_on'],
            ':created_at' => $invoice['created_at'],
        ]);

        if (!$isSuccessful) {
            throw new \RuntimeException('Unable to save invoice');
        }

        $invoice['id'] = $test->lastInsertId();
    }
}