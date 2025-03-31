<?php

declare(strict_types=1);

namespace Test\stubs\Date;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

final class FakeClock implements ClockInterface
{
    public const FAKE_TIME = '2025-04-01 18:00:00';

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable(self::FAKE_TIME);
    }
}