<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Command;

use Babymarkt\Symfony\CronBundle\Command\ValidateJobsCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidateJobsCommandTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(ValidateJobsCommand::class);

        $this->assertInstanceOf(ValidateJobsCommand::class, $instance);
    }
}
