<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\DependencyInjection;

use BabymarktExt\CronBundle\DependencyInjection\BabymarktExtCronExtension;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BabymarktExtCronExtensionTest
 * @package BabymarktExt\CronBundle\Tests\DependencyInjection
 */
class BabymarktExtCronExtensionTest extends TestCase
{

    /**
     * @var BabymarktExtCronExtension
     */
    private $extension;

    /**
     * @var string
     */
    private $root;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     *
     */
    public function testFullConfigInjection()
    {
        $config = [
            'options'  => [
                'id'      => 'test-id',
                'output'  => [
                    'file'   => 'test',
                    'append' => true
                ],
                'crontab' => [
                    'bin'     => 'test',
                    'tmpPath' => 'test',
                    'user'    => 'test',
                    'sudo'    => true
                ]
            ],
            'cronjobs' => [
                'test_cron' => [
                    'minutes'     => '1',
                    'hours'       => '2',
                    'days'        => '3',
                    'months'      => '4',
                    'weekdays'    => '5',
                    'command'     => 'babymarktext:cron:validate',
                    'description' => null,
                    'disabled'    => true,
                    'output'      => ['file' => 'test', 'append' => true],
                    'arguments'   => ['test1', 'test2', 'test3']
                ]
            ]
        ];

        $this->extension->load([$config], $this->container);

        $this->assertEquals($config['options']['output'], $this->container->getParameter($this->root . '.options.output'));
        $this->assertEquals($config['options']['crontab'], $this->container->getParameter($this->root . '.options.crontab'));
        $this->assertEquals($config['options']['id'], $this->container->getParameter($this->root . '.options.id'));

        foreach ($this->container->getParameter($this->root . '.definitions') as $alias => $def) {
            $this->assertArrayHasKey($alias, $config['cronjobs']);
            $this->assertEquals($config['cronjobs'][$alias], $def);
        }
    }

    /**
     *
     */
    public function testConfigWithDefaultValues()
    {
        $this->extension->load([], $this->container);

        $this->assertTrue($this->container->hasParameter($this->root . '.options.crontab'));
        $this->assertTrue($this->container->hasParameter($this->root . '.options.id'));
        $this->assertTrue($this->container->hasParameter($this->root . '.options.output'));

        $this->assertEquals('/test/dir:test', $this->container->getParameter($this->root . '.options.id'));
    }

    /**
     * @param $definition
     * @dataProvider cronDefinitionData
     */
    public function testDefaultCronDefinition($definition)
    {
        $defaults = [
            'minutes'     => '*',
            'hours'       => '*',
            'days'        => '*',
            'months'      => '*',
            'weekdays'    => '*',
            'command'     => 'babymarktext:cron:validate',
            'description' => null,
            'disabled'    => false,
            'output'      => ['file' => null, 'append' => null],
            'arguments'   => []
        ];

        $configs = [
            'cronjobs' => [
                'test-cron' => array_replace_recursive(['command' => 'babymarktext:cron:validate'], $definition)
            ]
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertEquals(
            ['test_cron' => array_replace_recursive($defaults, $definition)],
            $this->container->getParameter($this->root . '.definitions')
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new BabymarktExtCronExtension();
        $this->container = new ContainerBuilder();
        $this->root      = "babymarkt_ext_cron";

        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.project_dir', '/test/dir');
        $this->container->setParameter('kernel.environment', 'test');
        $this->container->setParameter('kernel.debug', 'false');
    }

    /**
     * @return array
     */
    public function cronDefinitionData(): array
    {
        return [
            [
                ['minutes' => 1]
            ],
            [
                ['hours' => 1]
            ],
            [
                ['days' => 1]
            ],
            [
                ['months' => 1]
            ],
            [
                ['weekdays' => 1]
            ]

        ];
    }

}
