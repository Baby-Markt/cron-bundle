<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Command;


use BabymarktExt\CronBundle\Command\DumpCommand;
use BabymarktExt\CronBundle\Service\CrontabEntryGenerator;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
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

        $cmd = new DumpCommand();
        $cmd->setCrontabEntryGenerator($generator);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:dump'));
        $tester->execute([]);

        $this->assertEquals('Line1' . PHP_EOL . 'Line2' . PHP_EOL, $tester->getDisplay());
    }
}
