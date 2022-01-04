<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;use Symfony\Component\Config\Definition\ConfigurationInterface;

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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('babymarkt_cron');
        $rootNode = $treeBuilder->getRootNode();

        /**
         * Casts the value to int.
         * @param mixed $value
         * @return string
         */
        $castToString = function ($value) {
            return (string) $value;
        };

        $rootNode
            ->children()
                ->arrayNode('options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('id')->defaultNull()->end()
                        ->scalarNode('script')->defaultValue('bin/console')->end()
                        ->scalarNode('working_dir')
                            ->defaultNull()
                            ->info('If not set, the project dir is used as working dir.')
                        ->end()
                        ->arrayNode('output')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('file')->defaultValue('/dev/null')->end()
                                ->booleanNode('append')->defaultTrue()->end()
                            ->end()
                        ->end()
                        ->arrayNode('crontab')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('bin')->defaultValue('crontab')->end()
                                ->scalarNode('tmpPath')->defaultValue(sys_get_temp_dir())->end()
                                ->scalarNode('user')->defaultNull()->end()
                                ->booleanNode('sudo')->defaultFalse()->end()
                            ->end()
                        ->end() // arrayNode: crontab
                    ->end() // children
                ->end() // arrayNode: options

                ->arrayNode('cronjobs')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(static function ($v) {
                                return ['command' => $v];
                            })
                        ->end()
                        ->children()
                            ->scalarNode('minutes')
                                ->defaultValue('*')
                                ->validate()->always($castToString)->end()
                            ->end()
                            ->scalarNode('hours')
                                ->defaultValue('*')
                                ->validate()->always($castToString)->end()
                            ->end()
                            ->scalarNode('days')
                                ->defaultValue('*')
                                ->validate()->always($castToString)->end()
                            ->end()
                            ->scalarNode('months')
                                ->defaultValue('*')
                                ->validate()->always($castToString)->end()
                            ->end()
                            ->scalarNode('weekdays')
                                ->defaultValue('*')
                                ->validate()->always($castToString)->end()
                            ->end()
                            ->scalarNode('command')->isRequired()->end()
                            ->scalarNode('description')->defaultNull()->end()
                            ->booleanNode('disabled')->defaultFalse()->end()
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
                        ->end() // children
                    ->end() // prototype: array
                ->end() // arrayNode: cronjobs
            ->end(); // children

        return $treeBuilder;
    }
}
