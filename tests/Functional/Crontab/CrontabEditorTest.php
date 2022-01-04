<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Functional\Crontab;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEditor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CrontabEditorTest extends KernelTestCase
{
    public function testServiceDefinition()
    {
        self::bootKernel();

        $container = static::getContainer();
        $instance = $container->get(CrontabEditor::class);

        $this->assertInstanceOf(CrontabEditor::class, $instance);
    }
}
