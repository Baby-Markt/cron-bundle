<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Command;


use Babymarkt\Symfony\CronBundle\Command\DumpJobsCommand;
use Babymarkt\Symfony\CronBundle\Crontab\CrontabEntryGenerator;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DumpCommandTest extends TestCase
{
    use ContainerTrait;

    public function testDumpEntries()
    {
        $generator = $this->getMockBuilder(CrontabEntryGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generator->expects($this->once())
            ->method('generateEntries')
            ->willReturn(['Line1', 'Line2']);

        $cmd = new DumpJobsCommand($generator);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:dump'));
        $tester->execute([]);

        $this->assertEquals('Line1' . PHP_EOL . 'Line2' . PHP_EOL, $tester->getDisplay());
    }
}
