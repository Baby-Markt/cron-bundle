<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Command;

use Babymarkt\Symfony\CronBundle\Command\DropJobsCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DropJobsCommandTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(DropJobsCommand::class);

        $this->assertInstanceOf(DropJobsCommand::class, $instance);
    }

}
