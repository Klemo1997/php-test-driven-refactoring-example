<?php

declare(strict_types=1);

namespace Test;

use Nekofar\Slim\Test\Traits\AppTestTrait;
use Pdo\Sqlite;

abstract class ContainerAwareTestCase extends \PHPUnit\Framework\TestCase
{
    use AppTestTrait;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $app = require __DIR__ . '/../src/app.php';
        $this->setUpApp($app);
    }

    final protected function setUp(): void
    {
        $this->truncateTables();
    }

    protected function truncateTables(): void
    {
        $this->getContainer()->get(Sqlite::class)->exec('DELETE FROM invoices;');
    }

    protected function getContainer(): \Psr\Container\ContainerInterface
    {
        return $this->app->getContainer();
    }
}