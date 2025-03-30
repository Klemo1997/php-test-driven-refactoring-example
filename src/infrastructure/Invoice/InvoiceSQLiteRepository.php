<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use PDO;
use Pdo\Sqlite;

final readonly class InvoiceSQLiteRepository
{
    public function __construct(private Sqlite $sqlite)
    {
    }

    public function create(array &$invoice): void
    {
        $statement = $this->sqlite->prepare(<<<SQL
            INSERT INTO invoices(amount, currency, vat, exchange_rate, issued_on, created_at)
            VALUES(:amount, :currency, :vat, :exchange_rate, :issued_on, :created_at)
            SQL);

        $isSuccessful = $statement->execute([
            ':amount' => $invoice['amount'],
            ':currency' => $invoice['currency'],
            ':vat' =>  $invoice['vat'],
            ':exchange_rate' => $invoice['exchange_rate'],
            ':issued_on' => $invoice['issued_on'],
            ':created_at' => $invoice['created_at'],
        ]);

        if (!$isSuccessful) {
            throw new \RuntimeException('Unable to save invoice');
        }

        $invoice['id'] = (int) $this->sqlite->lastInsertId();
    }
}