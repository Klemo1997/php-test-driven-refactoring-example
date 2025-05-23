<?php

declare(strict_types=1);

namespace Test\infrastructure\Invoice\ExchangeRate;

use App\domain\Invoice\ExchangeRate\UnableToFetchExchangeRateException;
use App\infrastructure\Invoice\ExchangeRate\NBSExchangeRateProvider;
use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;

final class NBSExchangeRateProviderTest extends TestCase
{
    private const UNSUPPORTED_SOURCE_CURRENCY = 'HUF';
    private const SOURCE_CURRENCY = 'EUR';
    private const UNSUPPORTED_TARGET_CURRENCY = 'BTC';
    private const TARGET_CURRENCY = 'CZK';
    private const SOURCE_TARGET_EXCHANGE_RATE = 25.048;
    private const ISSUED_DATE = '2023-03-25';

    private const SAMPLE_RESPONSE = <<<CSV
        Dátum;USD;JPY;BGN;CZK;DKK;GBP;HUF;PLN;RON;SEK;CHF;ISK;NOK;TRY;AUD;BRL;CAD;CNY;HKD;IDR;ILS;INR;KRW;MXN;MYR;NZD;PHP;SGD;THB;ZAR
        5.3.2025;1,0694;160,09;1,9558;25,048;7,4589;0,835;398,85;4,15;4,9758;11,0125;0,9514;146,5;11,82;38,9615;1,703;6,2938;1,5398;7,7675;8,3106;"17 496,99";3,875;93,0873;"1 551,65";22,0091;4,7369;1,8832;61,383;1,4301;36,012;19,7222
        CSV;

    private const INVALID_ISSUED_DATE = '1234-11-11';
    private const INVALID_DATE_RESPONSE = "\x48\x65\x6C";

    public function testFetchWithUnsupportedTargetCurrency(): void
    {
        $this->expectException(UnableToFetchExchangeRateException::class);

        new NBSExchangeRateProvider($this->getFakeClient())
            ->fetch(
                self::SOURCE_CURRENCY,
                self::UNSUPPORTED_TARGET_CURRENCY,
                new \DateTimeImmutable(self::ISSUED_DATE),
            );
    }

    public function testFetchWithUnsupportedSourceCurrency(): void
    {
        $this->expectException(UnableToFetchExchangeRateException::class);

        new NBSExchangeRateProvider($this->getFakeClient())
            ->fetch(
                self::UNSUPPORTED_SOURCE_CURRENCY,
                self::TARGET_CURRENCY,
                new \DateTimeImmutable(self::ISSUED_DATE),
            );
    }

    public function testFetch(): void
    {
        $actual_rate = new NBSExchangeRateProvider($this->getFakeClient())
            ->fetch(
                self::SOURCE_CURRENCY,
                self::TARGET_CURRENCY,
                new \DateTimeImmutable(self::ISSUED_DATE),
            );

        self::assertSame(self::SOURCE_TARGET_EXCHANGE_RATE, $actual_rate);
    }

    public function testFetchWithInvalidDate(): void
    {
        $this->expectException(UnableToFetchExchangeRateException::class);

        new NBSExchangeRateProvider($this->getFakeClient())
            ->fetch(
                self::SOURCE_CURRENCY,
                self::TARGET_CURRENCY,
                new \DateTimeImmutable(self::INVALID_ISSUED_DATE),
            );
    }

    public function getFakeClient(): ClientInterface
    {
        $mock = $this->createMock(ClientInterface::class);

        $mock->method('request')
            ->willReturnCallback(
                function ($method, $uri) {
                    $response = self::SAMPLE_RESPONSE;

                    if (str_contains($uri, self::INVALID_ISSUED_DATE)) {
                        $response = self::INVALID_DATE_RESPONSE;
                    }

                    return new ResponseFactory()
                        ->createResponse()
                        ->withStatus(StatusCodeInterface::STATUS_OK)
                        ->withBody(Utils::streamFor($response));
                }
            );

        return $mock;
    }
}