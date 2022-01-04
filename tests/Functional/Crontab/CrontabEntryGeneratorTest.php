<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEntryGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrontabEntryGeneratorTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(CrontabEntryGenerator::class);

        $this->assertInstanceOf(CrontabEntryGenerator::class, $instance);
    }
}
