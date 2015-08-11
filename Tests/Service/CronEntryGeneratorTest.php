<?php

namespace BabymarktExt\CronBundle\Tests\Service;

use BabymarktExt\CronBundle\Service\CronEntryGenerator;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 05.08.15
 * Time: 13:34
 */
class CronEntryGeneratorTest extends \PHPUnit_Framework_TestCase
{

    const ROOT_DIR    = '/root/dir';
    const ENVIRONMENT = 'test';

    protected $definitionDefaults = [
        'minutes'   => '*',
        'hours'     => '*',
        'days'      => '*',
        'months'    => '*',
        'weekdays'  => '*',
        'enabled'   => true,
        'output'    => null,
        'command'   => null,
        'arguments' => []
    ];

    protected $defaults = [
        'output' => '/dev/null'
    ];

    public function testDefaultValues()
    {
        $key = 'cron-def';

        $definition = [
            $key => array_merge($this->definitionDefaults, [
                'command' => 'babymarkt:test:command'
            ])
        ];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $entries   = $generator->generateEntries();

        $this->assertCount(1, $entries);
        $this->assertArrayHasKey($key, $entries);
        $this->assertEquals(
            sprintf('* * * * * cd %s; php console --env=%s babymarkt:test:command 2>&1 1>%s',
                self::ROOT_DIR, self::ENVIRONMENT, $this->defaults['output']),
            $entries[$key]
        );
    }

    public function testDisabledCron()
    {
        $key = 'cron-def';

        $definition = [
            $key => array_merge($this->definitionDefaults, [
                'command' => 'babymarkt:test:command',
                'enabled' => false
            ])
        ];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $entries   = $generator->generateEntries();

        $this->assertCount(0, $entries);
    }

    public function testIndividualOutputRedirection()
    {
        $key = 'cron-def';

        $definition = [
            $key => array_merge($this->definitionDefaults, [
                'command' => 'babymarkt:test:command',
                'output'  => '/var/log/log.log'
            ])
        ];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $entries   = $generator->generateEntries();

        $this->assertStringEndsWith('2>&1 1>/var/log/log.log', $entries[$key]);
    }

    public function testCronInterval()
    {
        $key = 'cron-def';

        $definition = [
            $key => array_merge($this->definitionDefaults, [
                'minutes'  => '1',
                'hours'    => '2',
                'days'     => '3',
                'months'   => '4',
                'weekdays' => '5',
                'command'  => 'babymarkt:test:command'
            ])
        ];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $entries   = $generator->generateEntries();

        $this->assertStringStartsWith('1 2 3 4 5', $entries[$key]);
    }

    public function testCommandArguments()
    {
        $key = 'cron-def';

        $definition = [
            $key => array_merge($this->definitionDefaults, [
                'command'   => 'babymarkt:test:command',
                'arguments' => ['--arg=5', '-d']
            ])
        ];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $entries   = $generator->generateEntries();

        $this->assertContains('babymarkt:test:command --arg=5 -d', $entries[$key]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoCommandSupplied()
    {
        $definition = ['some' => $this->definitionDefaults];

        $generator = new CronEntryGenerator($definition, $this->defaults, self::ROOT_DIR, self::ENVIRONMENT);
        $generator->generateEntries();
    }
}
