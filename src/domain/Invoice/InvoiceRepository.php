<?php

declare(strict_types=1);

namespace App\domain\Invoice;

interface InvoiceRepository
{
    public function create(array $invoice): array;

    public function findById(int $id): ?array;
}