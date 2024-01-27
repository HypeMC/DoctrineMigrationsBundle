<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MigrationsBundle\DependencyInjection\CompilerPass;

use Doctrine\Bundle\MigrationsBundle\AbstractServiceMigration;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\TypedReference;

use function is_subclass_of;

class RegisterMigrationsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $migrationRefs = [];

        foreach ($container->findTaggedServiceIds('doctrine_migrations.migration', true) as $id => $attributes) {
            $class = $container->getDefinition($id)->getClass();

            $definition = $container->setDefinition($id, new ChildDefinition('doctrine_migrations.abstract_migration'));
            $definition->setClass($class);

            if (is_subclass_of($class, AbstractServiceMigration::class)) {
                $definition->addTag('container.service_subscriber');
            }

            $migrationRefs[$id] = new TypedReference($id, $class);
        }

        if ($migrationRefs !== []) {
            $container->getDefinition('doctrine.migrations.filter_service_migration_finder')
                ->replaceArgument(1, new ServiceLocatorArgument($migrationRefs));
            $container->getDefinition('doctrine.migrations.service_migrations_repository')
                ->replaceArgument(1, new ServiceLocatorArgument($migrationRefs));
        } else {
            $container->removeDefinition('doctrine.migrations.filter_service_migration_finder');
            $container->removeDefinition('doctrine.migrations.service_migrations_repository');
        }
    }
}
