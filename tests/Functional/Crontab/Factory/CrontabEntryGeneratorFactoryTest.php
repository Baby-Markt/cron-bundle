<?php

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab\Factory;

use Babymarkt\Symfony\CronBundle\Crontab\Factory\CrontabEntryGeneratorFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrontabEntryGeneratorFactoryTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $factory = $container->get(CrontabEntryGeneratorFactory::class);

        $this->assertInstanceOf(CrontabEntryGeneratorFactory::class, $factory);
    }
}
