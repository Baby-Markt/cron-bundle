<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Command;

use BabymarktExt\CronBundle\Command\DropCommand;
use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;
use BabymarktExt\CronBundle\Service\CrontabEditor;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DropCommandTest
 * @package BabymarktExt\CronBundle\Tests\Command
 */
class DropCommandTest extends TestCase
{
    use ContainerTrait {
        getContainer as parentGetContainer;
    }

    const SERVICE_CRONTAB_EDITOR = 'babymarkt_ext_cron.service.crontabeditor';

    public function testTargetNotWritable()
    {
        $container = $this->getContainer();

        $cmd = new DropCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('removeCrons')
            ->willThrowException(new WriteException('test fail'));

        $tester = new CommandTester($app->find('babymarktext:cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t write to crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(DropCommand::STATUS_NOT_WRITABLE, $tester->getStatusCode());
    }

    public function testAccessDenied()
    {
        $container = $this->getContainer();

        $cmd = new DropCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('removeCrons')
            ->willThrowException(new AccessDeniedException('test fail'));

        $tester = new CommandTester($app->find('babymarktext:cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('Can\'t access crontab.', $tester->getDisplay());
        $this->assertStringContainsString('test fail', $tester->getDisplay());
        $this->assertEquals(DropCommand::STATUS_ACCESS_DENIED, $tester->getStatusCode());
    }

    public function testSuccessfulDrop()
    {
        $container = $this->getContainer();

        $cmd = new DropCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('removeCrons')
            ->willReturn(null);

        $tester = new CommandTester($app->find('babymarktext:cron:drop'));
        $tester->execute([]);

        $this->assertStringContainsString('All crons successfully dropped.', $tester->getDisplay());
        $this->assertEquals(0, $tester->getStatusCode());
    }

    /**
     * @param array $config
     * @return ContainerBuilder
     */
    protected function getContainer($config = [])
    {
        $container = $this->parentGetContainer($config);

        $editor = $this->getMockBuilder(CrontabEditor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->set(self::SERVICE_CRONTAB_EDITOR, $editor);

        return $container;
    }
}
