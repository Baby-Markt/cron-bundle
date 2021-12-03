<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BabymarktExtCronExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Check if Doctrine is available and reporting is enabled.
        $availableBundles = $container->getParameter('kernel.bundles');
        $container->setParameter(
            'babymarkt_ext_cron.report.enabled',
            isset($availableBundles['DoctrineBundle']) && $config['report']['enabled']
        );

        // Only enable reporting if parameter is set to true.
        if ($container->getParameter('babymarkt_ext_cron.report.enabled')) {
            $loader->load('services.report.yml');

            $container->setParameter('babymarkt_ext_cron.report.database.user', $config['report']['database']['user']);
            $container->setParameter('babymarkt_ext_cron.report.database.password', $config['report']['database']['password']);
            $container->setParameter('babymarkt_ext_cron.report.database.path', $config['report']['database']['path']);
        }

        // Define cron block id if not set.
        $id = $config['options']['id'] ?: sprintf(
            '%s:%s',
            $container->getParameter('kernel.root_dir'),
            $container->getParameter('kernel.environment')
        );

        $container->setParameter('babymarkt_ext_cron.definitions', $config['crons']);

        $container->setParameter('babymarkt_ext_cron.options.output', $config['options']['output']);
        $container->setParameter('babymarkt_ext_cron.options.crontab', $config['options']['crontab']);
        $container->setParameter('babymarkt_ext_cron.options.script', $config['options']['script']);
        $container->setParameter('babymarkt_ext_cron.options.working_dir', $config['options']['working_dir']);
        $container->setParameter('babymarkt_ext_cron.options.id', $id);
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('doctrine.yml');
        }
    }
}
