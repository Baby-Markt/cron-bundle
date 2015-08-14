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
use BabymarktExt\CronBundle\Service\CronEntryGenerator;
use BabymarktExt\CronBundle\Service\CrontabEditor;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SyncCommandTest
 * @package BabymarktExt\CronBundle\Tests\Command
 */
class SyncCommandTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait {
        getContainer as parentGetContainer;
    }

    const
        SERVICE_ENTRY_GENERATOR = 'babymarkt_ext_cron.service.cronentrygenerator',
        SERVICE_CRONTAB_EDITOR = 'babymarkt_ext_cron.service.crontabeditor';


    public function testTargetNotWritable()
    {
        $container = $this->getContainer();

        $cmd = new SyncCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var \PHPUnit_Framework_MockObject_MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('injectCrons')
            ->willThrowException(new WriteException('test fail'));

        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute([]);

        $this->assertContains('Can\'t write to crontab.', $tester->getDisplay());
        $this->assertContains('test fail', $tester->getDisplay());
        $this->assertEquals(SyncCommand::STATUS_NOT_WRITABLE, $tester->getStatusCode());
    }

    public function testAccessDenied()
    {
        $container = $this->getContainer();

        $cmd = new SyncCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var \PHPUnit_Framework_MockObject_MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('injectCrons')
            ->willThrowException(new AccessDeniedException('test fail'));

        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertContains('Can\'t access crontab.', $tester->getDisplay());
        $this->assertContains('test fail', $tester->getDisplay());
        $this->assertEquals(SyncCommand::STATUS_ACCESS_DENIED, $tester->getStatusCode());
    }

    public function testSuccessfulSync()
    {
        $container = $this->getContainer();

        $cmd = new SyncCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        /** @var \PHPUnit_Framework_MockObject_MockObject $editor */
        $editor = $container->get(self::SERVICE_CRONTAB_EDITOR);
        $editor->expects($this->once())
            ->method('injectCrons')
            ->willReturn(null);

        $tester = new CommandTester($app->find('babymarktext:cron:sync'));
        $tester->execute(['command' => 'babymarkt:cron:sync']);

        $this->assertContains('3 crons successfully synced.', $tester->getDisplay());
        $this->assertEquals(0, $tester->getStatusCode());
    }

    /**
     * @param array $config
     * @return ContainerBuilder
     */
    protected function getContainer($config = [])
    {
        $container = $this->parentGetContainer($config);

        /** @var CronEntryGenerator|\PHPUnit_Framework_MockObject_MockObject $entryGenerator */
        $entryGenerator = $this->getMockBuilder(CronEntryGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryGenerator->expects($this->once())
            ->method('generateEntries')
            ->willReturn([1, 2, 3]);

        $editor = $this->getMockBuilder(CrontabEditor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->set(self::SERVICE_ENTRY_GENERATOR, $entryGenerator);
        $container->set(self::SERVICE_CRONTAB_EDITOR, $editor);

        return $container;
    }


}
