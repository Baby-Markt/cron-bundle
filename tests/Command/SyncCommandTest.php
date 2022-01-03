<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Command;

use Babymarkt\Symfony\CronBundle\Command\SyncJobsCommand;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Service\CrontabEditor;
use Babymarkt\Symfony\CronBundle\Service\CrontabEntryGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SyncCommandTest extends TestCase
{
    /**
     * @var CrontabEntryGenerator|MockObject
     */
    private $entryGenerator;

    /**
     * @var CrontabEditor|MockObject
     */
    private $editor;

    protected function setUp(): void
    {
        /** @var CrontabEntryGenerator|MockObject $entryGenerator */
        $this->entryGenerator = $this->getMockBuilder(CrontabEntryGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entryGenerator->expects($this->once())
            ->method('generateEntries')
            ->willReturn([1, 2, 3]);

        $this->editor = $this->getMockBuilder(CrontabEditor::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testTargetNotWritable()
    {
        $this->editor->expects($this->once())
            ->method('injectCronjobs')
            ->willThrowException(new WriteException('test fail'));

        $cmd = new SyncJobsCommand($this->entryGenerator, $this->editor);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:sync'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t write to crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(SyncJobsCommand::EXITCODE_NOT_WRITABLE, $tester->getStatusCode());
    }

    public function testAccessDenied()
    {
        $this->editor->expects($this->once())
            ->method('injectCronjobs')
            ->willThrowException(new AccessDeniedException('test fail'));

        $cmd = new SyncJobsCommand($this->entryGenerator, $this->editor);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertStringContainsString('Can\'t access crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(SyncJobsCommand::EXITCODE_ACCESS_DENIED, $tester->getStatusCode());
    }

    public function testSuccessfulSync()
    {
        $this->editor->expects($this->once())
            ->method('injectCronjobs');

        $cmd = new SyncJobsCommand($this->entryGenerator, $this->editor);

        $app = new Application();
        $app->add($cmd);

        /** @var MockObject $editor */
        $tester = new CommandTester($app->find('babymarkt-cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertStringContainsString('3 cronjobs successfully synced.', $tester->getDisplay());
        $this->assertEquals(0, $tester->getStatusCode());
    }


}
