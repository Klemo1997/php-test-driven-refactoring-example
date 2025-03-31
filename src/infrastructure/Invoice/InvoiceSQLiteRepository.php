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

    public function create(array $invoice): array
    {
        $statement = $this->sqlite->prepare(<<<SQL
            INSERT INTO invoices(amount, currency, vat, exchange_rate, issued_on, created_at)
            VALUES(:amount, :currency, :vat, :exchange_rate, :issued_on, :created_at)
            SQL);

        $is_successful = $statement->execute([
            ':amount' => $invoice['amount'],
            ':currency' => $invoice['currency'],
            ':vat' =>  $invoice['vat'],
            ':exchange_rate' => $invoice['exchange_rate'],
            ':issued_on' => $invoice['issued_on'],
            ':created_at' => $invoice['created_at'],
        ]);

        if (!$is_successful) {
            throw new \RuntimeException('Unable to save invoice');
        }

        $invoice['id'] = (int) $this->sqlite->lastInsertId();

        return $invoice;
    }


    public function findById(int $id): ?array
    {
        $statement = $this->sqlite->prepare(<<<SQL
            SELECT id, amount, currency, exchange_rate, vat, issued_on, created_at
            FROM invoices
            WHERE id = :id
            SQL);

        $statement->execute([':id' => $id]);
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);

        if ($invoice === []) {
            return null;
        }

        return [
            'id' => (int) $invoice['id'],
            'amount' => $invoice['amount'],
            'currency' => $invoice['currency'],
            'exchange_rate' => $invoice['exchange_rate'],
            'vat' => $invoice['vat'],
            'issued_on' => $invoice['issued_on'],
            'created_at' => $invoice['created_at'],
        ];
    }
}