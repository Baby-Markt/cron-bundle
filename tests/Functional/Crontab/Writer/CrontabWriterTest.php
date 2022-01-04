<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab\Writer;

use Babymarkt\Symfony\CronBundle\Crontab\Writer\CrontabWriter;
use Babymarkt\Symfony\CronBundle\Crontab\Writer\CrontabWriterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrontabWriterTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(CrontabWriterInterface::class);

        $this->assertInstanceOf(CrontabWriter::class, $instance);
    }
}
