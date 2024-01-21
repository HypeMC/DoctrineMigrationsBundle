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
            if (is_subclass_of($class, AbstractServiceMigration::class)) {
                $definition = new ChildDefinition('doctrine_migrations.abstract_migration');
                $definition->setClass($class);
                $definition->addTag('container.service_subscriber');

                $container->setDefinition($id, $definition);

                $migrationRefs[$id] = new TypedReference($id, $class);
            } else {
                $container->removeDefinition($id);
            }
        }

        if ($migrationRefs !== []) {
            $container->getDefinition('doctrine.migrations.service_migrations_factory')
                ->replaceArgument(1, new ServiceLocatorArgument($migrationRefs));
        } else {
            $container->removeDefinition('doctrine.migrations.service_migrations_factory');
        }
    }
}
