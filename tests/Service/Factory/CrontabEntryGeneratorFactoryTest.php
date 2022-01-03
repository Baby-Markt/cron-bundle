<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\Tests\Service\Factory;


use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Babymarkt\Symfony\CronBundle\Service\CrontabEntryGenerator;
use Babymarkt\Symfony\CronBundle\Service\Factory\CrontabEntryGeneratorFactory;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;

class CrontabEntryGeneratorFactoryTest extends TestCase
{
    use ContainerTrait;


    public function testFactory()
    {
        $config = [
            'cronjobs' => [
                'test-job' => [
                    'command'  => 'babymarktext:test:command'
                ]
            ]
        ];

        $container = $this->getContainer($config);

        $factory = new CrontabEntryGeneratorFactory(
            $container->getParameter('babymarkt_cron.definitions'),
            $container->getParameter('babymarkt_cron.options.output'),
            $container->getParameter('kernel.project_dir') . '/..',
            $container->getParameter('kernel.environment')
        );

        $generator = $factory->create();

        $this->assertInstanceOf(CrontabEntryGenerator::class, $generator);
        $this->assertContainsOnlyInstancesOf(Definition::class, $generator->getDefinitions());
    }
}
