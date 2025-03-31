<?php

declare(strict_types=1);

namespace Test\domain\Invoice\Vat;

use App\domain\Invoice\Vat\SlovakVatProvider;
use App\domain\Invoice\Vat\UnableToFetchVatException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SlovakVatProviderTest extends TestCase
{
    const string DATE_BEFORE_1993 = '1992-12-31';

    public function testFetchBefore1993(): void
    {
        $this->expectException(UnableToFetchVatException::class);
        new SlovakVatProvider()->fetch(new \DateTimeImmutable(self::DATE_BEFORE_1993));
    }

    public static function fetchVatProvider(): \Generator
    {
        yield 'From 1993 it returns 23' => [
            'expected' => 23.0,
            'date' => '1993-01-01',
        ];

        yield 'In 1994, but before 1.8., it returns 23' => [
            'expected' => 23.0,
            'date' => '1994-07-31',
        ];

        yield 'In 1994, from 1.8. on after, it returns 25' => [
            'expected' => 25.0,
            'date' => '1994-08-01',
        ];

        yield 'At the last day of 1995, it returns 25' => [
            'expected' => 25.0,
            'date' => '1995-12-31',
        ];

        yield 'At the first day of 1996, it returns 23' => [
            'expected' => 23.0,
            'date' => '1996-01-01',
        ];

        yield 'At the last day of 2002, it returns 23' => [
            'expected' => 23.0,
            'date' => '2002-12-31',
        ];

        yield 'At the first day of 2003, it returns 20' => [
            'expected' => 20.0,
            'date' => '2003-01-01',
        ];

        yield 'At the last day of 2003, it returns 20' => [
            'expected' => 20.0,
            'date' => '2003-01-01',
        ];

        yield 'At the first day of 2004, it returns 20' => [
            'expected' => 20.0,
            'date' => '2003-01-01',
        ];

        yield 'At the last day of 2009, it returns 19' => [
            'expected' => 19.0,
            'date' => '2009-12-31',
        ];

        yield 'At the last day of 2024, it returns 20' => [
            'expected' => 20.0,
            'date' => '2024-12-31',
        ];

        yield 'At the first day of 2025, it returns 23 :(' => [
            'expected' => 23.0,
            'date' => '2025-01-01',
        ];
    }

    #[DataProvider('fetchVatProvider')]
    public function testFetch(float $expected, string $date): void
    {
        self::assertEquals($expected, new SlovakVatProvider()->fetch(new \DateTimeImmutable($date)));
    }
}