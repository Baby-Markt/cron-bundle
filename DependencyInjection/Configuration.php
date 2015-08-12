<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('babymarkt_ext_cron');

        $rootNode
            ->children()
                ->arrayNode('options')
                    ->children()
                        ->scalarNode('id')->defaultNull()->end()
                        ->arrayNode('output')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('file')->defaultValue('/dev/null')->end()
                                ->booleanNode('append')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('crontab')
                            ->children()
                                ->scalarNode('bin')->defaultValue('crontab')->end()
                                ->scalarNode('tmpPath')->defaultValue(sys_get_temp_dir())->end()
                                ->scalarNode('user')->defaultNull()->end()
                                ->booleanNode('sudo')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('crons')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('minutes')->defaultValue('*')->end()
                            ->scalarNode('hours')->defaultValue('*')->end()
                            ->scalarNode('days')->defaultValue('*')->end()
                            ->scalarNode('months')->defaultValue('*')->end()
                            ->scalarNode('weekdays')->defaultValue('*')->end()
                            ->scalarNode('command')->isRequired()->end()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->arrayNode('output')
                                ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('file')->defaultNull()->end()
                                        ->booleanNode('append')->defaultNull()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('arguments')
                                    ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
