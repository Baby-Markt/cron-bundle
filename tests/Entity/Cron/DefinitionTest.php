<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Entity\Cron;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{

    protected array $properties = [
        'minutes'   => "1",
        'hours'     => "1",
        'days'      => "1",
        'months'    => "1",
        'weekdays'  => "1",
        'command'   => 'dummy:command',
        'disabled'  => true,
        'output'    => [
            'file'   => 'dummy.txt',
            'append' => true
        ],
        'arguments' => [
            '--test', '--f file', '-v'
        ]
    ];

    public function testPropertiesSet()
    {

        $def = new Definition();
        $def->setProperties($this->properties);

        foreach ($this->properties as $key => $expected) {
            if (is_bool($expected)) {
                $getter = 'is' . ucfirst($key);
                $this->assertTrue($def->$getter());
            } else {
                $getter = 'get' . ucfirst($key);
                $this->assertEquals($expected, $def->$getter());
            }
        }
    }

    public function testInvalidProperty()
    {
        $this->expectExceptionMessage("Unknown property unknown given.");
        $this->expectException(\InvalidArgumentException::class);
        $def = new Definition();
        $def->setProperties(['unknown' => 'value']);
    }

    public function testConstructorPropertyInjection()
    {

        $def = new Definition($this->properties);

        foreach ($this->properties as $key => $expected) {
            if (is_bool($expected)) {
                $getter = 'is' . ucfirst($key);
                $this->assertTrue($def->$getter());
            } else {
                $getter = 'get' . ucfirst($key);
                $this->assertEquals($expected, $def->$getter());
            }
        }
    }


}
