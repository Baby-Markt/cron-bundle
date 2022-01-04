<?php

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab;

use Babymarkt\Symfony\CronBundle\Crontab\DefinitionChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DefinitionCheckerTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(DefinitionChecker::class);

        $this->assertInstanceOf(DefinitionChecker::class, $instance);
    }
}
