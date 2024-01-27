<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MigrationsBundle;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class AbstractServiceMigration extends AbstractMigration implements ServiceSubscriberInterface
{
    /** @var ContainerInterface */
    protected $container;

    final public function __construct(Connection $connection, LoggerInterface $logger, ContainerInterface $container)
    {
        parent::__construct($connection, $logger);

        $this->container = $container;
    }
}
