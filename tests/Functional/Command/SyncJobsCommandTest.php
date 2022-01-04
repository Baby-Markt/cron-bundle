<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Command;

use Babymarkt\Symfony\CronBundle\Command\SyncJobsCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SyncJobsCommandTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(SyncJobsCommand::class);

        $this->assertInstanceOf(SyncJobsCommand::class, $instance);
    }
}
