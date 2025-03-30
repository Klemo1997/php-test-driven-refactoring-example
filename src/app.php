<?php

use App\infrastructure\Invoice\InvoiceController;
use DI\Bridge\Slim\Bridge;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$_ENV['APP_ENV'] ??= 'dev';

$container_config = require __DIR__ . '/../src/config/config.php';

$env_container_config = match ($_ENV['APP_ENV']) {
    'test' => require __DIR__ . '/../src/config/test.php',
    'dev' => require __DIR__ . '/../src/config/dev.php',
    default => throw new RuntimeException('Missing config for env: ' . $_ENV['APP_ENV']),
};

$container = new \DI\Container(array_merge($container_config, $env_container_config));

$app = Bridge::create($container);

// Define Custom Error Handler
$api_error_handler = function (ServerRequestInterface $request, Throwable $exception) use ($app) {
    $payload = ['error' => $exception->getMessage(), 'throwable' => $exception->getTraceAsString()];

    return $app->getResponseFactory()->createResponse()
        ->withStatus(\Fig\Http\Message\StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR)
        ->withBody(new HttpFactory()->createStream(json_encode($payload, JSON_UNESCAPED_UNICODE)));
};

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_middleware->setDefaultErrorHandler($api_error_handler);

$app->post('/invoice', [InvoiceController::class, 'addInvoice']);

return $app;