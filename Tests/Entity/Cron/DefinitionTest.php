<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Entity\Cron;


use BabymarktExt\CronBundle\Entity\Cron\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{

    protected $properties = [
        'minutes'   => 1,
        'hours'     => 1,
        'days'      => 1,
        'months'    => 1,
        'weekdays'  => 1,
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
                $this->assertSame($expected, $def->$getter());
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
                $this->assertSame($expected, $def->$getter());
            }
        }
    }


}
