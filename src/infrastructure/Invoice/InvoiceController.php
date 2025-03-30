<?php

declare(strict_types=1);

namespace App\infrastructure\Invoice;

use Fig\Http\Message\StatusCodeInterface;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class InvoiceController
{
    public function __construct(private AddInvoiceUseCase $addInvoiceUseCase) {
    }

    public function addInvoice(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $invoice = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $added_invoice = $this->addInvoiceUseCase->execute($invoice);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withBody(new HttpFactory()->createStream(json_encode($added_invoice, JSON_THROW_ON_ERROR)))
            ->withStatus(StatusCodeInterface::STATUS_CREATED);
    }
}