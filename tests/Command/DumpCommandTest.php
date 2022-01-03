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


use Babymarkt\Symfony\CronBundle\Command\DumpJobsCommand;
use Babymarkt\Symfony\CronBundle\Service\CrontabEntryGenerator;
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

        $cmd = new DumpJobsCommand();
        $cmd->setCrontabEntryGenerator($generator);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:dump'));
        $tester->execute([]);

        $this->assertEquals('Line1' . PHP_EOL . 'Line2' . PHP_EOL, $tester->getDisplay());
    }
}
