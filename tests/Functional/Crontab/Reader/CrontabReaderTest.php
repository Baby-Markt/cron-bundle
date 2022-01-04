<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab\Reader;

use Babymarkt\Symfony\CronBundle\Crontab\Reader\CrontabReader;
use Babymarkt\Symfony\CronBundle\Crontab\Reader\CrontabReaderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrontabReaderTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(CrontabReaderInterface::class);

        $this->assertInstanceOf(CrontabReader::class, $instance);
    }
}
