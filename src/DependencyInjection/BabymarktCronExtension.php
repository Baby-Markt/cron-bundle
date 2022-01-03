<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BabymarktCronExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Define cron block id if not set.
        $id = $config['options']['id'] ?? sprintf(
            '%s:%s',
            $container->getParameter('kernel.project_dir'),
            $container->getParameter('kernel.environment')
        );

        // Define the working dir.
        $workingDir = $config['options']['working_dir'] ?? $container->getParameter('kernel.project_dir');

        $container->setParameter('babymarkt_cron.definitions', $config['cronjobs']);
        $container->setParameter('babymarkt_cron.options.output', $config['options']['output']);
        $container->setParameter('babymarkt_cron.options.crontab', $config['options']['crontab']);
        $container->setParameter('babymarkt_cron.options.script', $config['options']['script']);
        $container->setParameter('babymarkt_cron.options.working_dir', $workingDir);
        $container->setParameter('babymarkt_cron.options.id', $id);
    }
}
