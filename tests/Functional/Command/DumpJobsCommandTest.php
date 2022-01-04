<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Command;

use Babymarkt\Symfony\CronBundle\Command\DumpJobsCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DumpJobsCommandTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(DumpJobsCommand::class);

        $this->assertInstanceOf(DumpJobsCommand::class, $instance);
    }
}
