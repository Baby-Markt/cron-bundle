<?php

namespace Babymarkt\Symfony\CronBundle\DependencyInjection\Compiler;

use Babymarkt\Symfony\CronBundle\Crontab\DefinitionChecker;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandDefinitionPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DefinitionChecker::class)) {
            return;
        }

        $definition = $container->findDefinition(DefinitionChecker::class);

        $commandIds = $container->findTaggedServiceIds('console.command');

        foreach ($commandIds as $id => $tags) {
            $definition->addMethodCall('addCommand', [new Reference($id)]);
        }
    }
}
