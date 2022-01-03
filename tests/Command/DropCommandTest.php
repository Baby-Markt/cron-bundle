<?php
declare(strict_types=1);
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\Tests\Command;

use Babymarkt\Symfony\CronBundle\Command\DropJobsCommand;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Service\CrontabEditor;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class DropCommandTest
 * @package Babymarkt\Symfony\CronBundle\Tests\Command
 */
class DropCommandTest extends TestCase
{
    use ContainerTrait {
        getContainer as parentGetContainer;
    }

    /**
     * @var CrontabEditor|MockObject
     */
    private $crontabEditor;

    protected function setUp(): void
    {
        $this->crontabEditor = $this->getMockBuilder(CrontabEditor::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testTargetNotWritable()
    {
        $this->crontabEditor->expects($this->once())
            ->method('removeCronjobs')
            ->willThrowException(new WriteException('test fail'));

        $cmd = new DropJobsCommand();
        $cmd->setCrontabEditor($this->crontabEditor);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t write to crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(DropJobsCommand::STATUS_NOT_WRITABLE, $tester->getStatusCode());
    }

    public function testAccessDenied()
    {
        $this->crontabEditor->expects($this->once())
            ->method('removeCronjobs')
            ->willThrowException(new AccessDeniedException('test fail'));

        $cmd = new DropJobsCommand();
        $cmd->setCrontabEditor($this->crontabEditor);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t access crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(DropJobsCommand::STATUS_ACCESS_DENIED, $tester->getStatusCode());
    }

    public function testSuccessfulDrop()
    {
        /** @var MockObject $editor */
        $this->crontabEditor->expects($this->once())
            ->method('removeCronjobs');

        $cmd = new DropJobsCommand();
        $cmd->setCrontabEditor($this->crontabEditor);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('All cronjobs successfully dropped.', $tester->getDisplay());
        $this->assertEquals(0, $tester->getStatusCode());
    }
}
