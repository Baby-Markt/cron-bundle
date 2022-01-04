<?php

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Shell;

use Babymarkt\Symfony\CronBundle\Shell\ShellWrapper;
use Babymarkt\Symfony\CronBundle\Shell\ShellWrapperInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShellWrapperTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance  = $container->get(ShellWrapperInterface::class);

        $this->assertInstanceOf(ShellWrapper::class, $instance);
    }
}
