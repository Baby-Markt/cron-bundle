<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Command;

use BabymarktExt\CronBundle\Command\SyncCommand;
use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;
use BabymarktExt\CronBundle\Service\CrontabEditor;
use BabymarktExt\CronBundle\Service\CrontabEntryGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class SyncCommandTest
 * @package BabymarktExt\CronBundle\Tests\Command
 */
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

        $cmd = new SyncCommand();
        $cmd->setCrontabEditor($this->editor);
        $cmd->setCrontabEntryGenerator($this->entryGenerator);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t write to crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(SyncCommand::STATUS_NOT_WRITABLE, $tester->getStatusCode());
    }

    public function testAccessDenied()
    {
        $this->editor->expects($this->once())
            ->method('injectCronjobs')
            ->willThrowException(new AccessDeniedException('test fail'));

        $cmd = new SyncCommand();
        $cmd->setCrontabEditor($this->editor);
        $cmd->setCrontabEntryGenerator($this->entryGenerator);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertStringContainsString('Can\'t access crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(SyncCommand::STATUS_ACCESS_DENIED, $tester->getStatusCode());
    }

    public function testSuccessfulSync()
    {
        $this->editor->expects($this->once())
            ->method('injectCronjobs');

        $cmd = new SyncCommand();
        $cmd->setCrontabEditor($this->editor);
        $cmd->setCrontabEntryGenerator($this->entryGenerator);

        $app = new Application();
        $app->add($cmd);

        /** @var MockObject $editor */
        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertStringContainsString('3 cronjobs successfully synced.', $tester->getDisplay());
        $this->assertEquals(0, $tester->getStatusCode());
    }


}
