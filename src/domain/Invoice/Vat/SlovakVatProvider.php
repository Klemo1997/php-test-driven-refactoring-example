<?php

declare(strict_types=1);

namespace App\domain\Invoice\Vat;

final class SlovakVatProvider implements VatProvider
{
    public function fetch(\DateTimeImmutable $date): float
    {
        return match(true) {
            $this->isBefore($date, '1993-01-01') =>
                throw new UnableToFetchVatException("Country didn't officially exist yet!"),
            $this->isBefore($date, '1994-08-01') => 23.0,
            $this->isBefore($date, '1996-01-01') => 25.0,
            $this->isBefore($date, '2003-01-01') => 23.0,
            $this->isBefore($date, '2004-01-01') => 20.0,
            $this->isBefore($date, '2010-01-01') => 19.0,
            $this->isBefore($date, '2025-01-01') => 20.0,
            default => 23.0,
        };
    }

    private function isBefore(\DateTimeImmutable $date, string $thresholdDateString): bool
    {
        return $date < new \DateTimeImmutable($thresholdDateString);
    }
}