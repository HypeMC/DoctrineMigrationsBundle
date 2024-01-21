<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MigrationsBundle\MigrationsFactory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Psr\Container\ContainerInterface;

class ServiceMigrationFactory implements MigrationFactory
{
    /** @var MigrationFactory */
    private $migrationFactory;

    /** @var ContainerInterface */
    private $container;

    public function __construct(MigrationFactory $migrationFactory, ContainerInterface $container)
    {
        $this->migrationFactory = $migrationFactory;
        $this->container        = $container;
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        if ($this->container->has($migrationClassName)) {
            return $this->container->get($migrationClassName);
        }

        return $this->migrationFactory->createVersion($migrationClassName);
    }
}
