<?php

namespace BabymarktExt\CronBundle\Tests\Service;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Service\CronEntryGenerator;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 05.08.15
 * Time: 13:34
 */
class CronEntryGeneratorTest extends TestCase
{

    use ContainerTrait;

    /**
     * @var string
     */
    private $root;

    public function testDefaultValues()
    {
        $key = 'cron_def';

        $config = [
            'crons' => [
                $key => [
                    'command' => 'babymarktext:test:command'
                ]
            ]
        ];

        $container   = $this->getContainer($config);
        $definitions = $container->getParameter($this->root . '.definitions');
        $outputConf  = $container->getParameter($this->root . '.options.output');

        $rootDir     = $container->getParameter('kernel.root_dir');
        $environment = $container->getParameter('kernel.environment');

        $generator = new CronEntryGenerator(
            array_map(
                function ($def) {
                    return new Definition($def);
                },
                $definitions
            ),
            $outputConf, $rootDir, $environment
        );
        $entries   = $generator->generateEntries();

        $this->assertCount(1, $entries);
        $this->assertArrayHasKey($key, $entries);
        $this->assertEquals(
            sprintf('* * * * * cd %s; php console --env=%s babymarktext:test:command 2>&1 1>%s',
                $rootDir, $environment, $outputConf['file']),
            $entries[$key]
        );
    }

    public function testDisabledCron()
    {
        $key = 'cron_def';

        $config = [
            'crons' => [
                $key => [
                    'command'  => 'babymarktext:test:command',
                    'disabled' => true
                ]
            ]
        ];

        $generator = $this->createGenerator($config);
        $entries   = $generator->generateEntries();

        $this->assertCount(0, $entries);
    }

    public function testOutputRedirection()
    {
        $key  = 'cron_def';
        $key1 = 'cron_append';
        $key2 = 'cron_replace';

        $config = [
            'crons' => [
                $key  => [
                    'command' => 'babymarktext:test:command',
                    'output'  => ['file' => '/var/log/log.log']
                ],
                $key1 => [
                    'command' => 'babymarktext:test:command',
                    'output'  => [
                        'file'   => 'test',
                        'append' => true
                    ]
                ],
                $key2 => [
                    'command' => 'babymarktext:test:command',
                    'output'  => [
                        'file'   => 'test',
                        'append' => false
                    ]
                ]
            ]
        ];

        $generator = $this->createGenerator($config);
        $entries   = $generator->generateEntries();

        $this->assertStringEndsWith('2>&1 1>/var/log/log.log', $entries[$key]);
        $this->assertStringContainsString("2>&1 1>>test", $entries[$key1]);
        $this->assertStringContainsString("2>&1 1>test", $entries[$key2]);
    }

    public function testCronInterval()
    {
        $key = 'cron_def';

        $config = [
            'crons' => [
                $key => [
                    'minutes'  => '1',
                    'hours'    => '2',
                    'days'     => '3',
                    'months'   => '4',
                    'weekdays' => '5',
                    'command'  => 'babymarktext:test:command'
                ]
            ]
        ];

        $generator = $this->createGenerator($config);
        $entries   = $generator->generateEntries();

        $this->assertStringStartsWith('1 2 3 4 5', $entries[$key]);
    }

    public function testCommandArguments()
    {
        $key = 'cron_def';

        $config = [
            'crons' => [
                $key => [
                    'command'   => 'babymarktext:test:command',
                    'arguments' => ['--arg=5', '-d']
                ]
            ]
        ];

        $generator = $this->createGenerator($config);
        $entries   = $generator->generateEntries();

        $this->assertStringContainsString('babymarktext:test:command --arg=5 -d', $entries[$key]);
    }

    public function testNoCommandSupplied()
    {
        $this->expectException(\InvalidArgumentException::class);
        $key = 'cron_def';

        $config = [
            'crons' => [
                $key => [
                    'command' => 'babymarktext:test:command'
                ]
            ]
        ];

        $container = $this->getContainer($config);
        /** @var Definition[] $definitions */
        $definitions = $container->getParameter($this->root . '.definitions');
        $outputConf  = $container->getParameter($this->root . '.options.output');

        $definitions = array_map(
            function ($def) {
                return new Definition($def);
            },
            $definitions
        );

        // Remove command to test exception
        $definitions[$key]->setCommand(null);

        $rootDir     = $container->getParameter('kernel.root_dir');
        $environment = $container->getParameter('kernel.environment');

        $generator = new CronEntryGenerator($definitions, $outputConf, $rootDir, $environment);
        $generator->generateEntries();
    }

    /**
     * @param $config
     * @return CronEntryGenerator
     */
    protected function createGenerator($config)
    {
        $container   = $this->getContainer($config);
        $definitions = $container->getParameter($this->root . '.definitions');
        $outputConf  = $container->getParameter($this->root . '.options.output');

        $rootDir     = $container->getParameter('kernel.root_dir');
        $environment = $container->getParameter('kernel.environment');

        $definitions = array_map(
            function ($def) {
                return new Definition($def);
            },
            $definitions
        );

        $generator = new CronEntryGenerator($definitions, $outputConf, $rootDir, $environment);

        return $generator;
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->root = 'babymarkt_ext_cron';
    }
}
