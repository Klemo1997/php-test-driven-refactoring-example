<?php

declare(strict_types=1);

namespace App\domain\Invoice\Vat;

interface VatProvider
{
    /**
     * @throws UnableToFetchVatException
     */
    public function fetch(\DateTimeImmutable $date): float;
}