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
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DumpCommandTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;

    public function testDumpEntries()
    {
        $cmd = new DumpCommand();
        $cmd->setContainer($this->getContainer([
            'crons' => [
                'test' => [
                    'command' => 'babymarkt:test:command'
                ]
            ]
        ]));

        $app = new Application();
        $app->add($cmd);

        // Check if command is exists.
        $command = $app->find('babymarktext:cron:dump');
        $this->assertInstanceOf(DumpCommand::class, $command);

        $tester = new CommandTester($command);
        $tester->execute(['command' => $command->getName()]);

        $this->assertEquals(
            '* * * * * cd /test/path/..; php console --env=test babymarkt:test:command 2>&1 1>/dev/null',
            trim($tester->getDisplay())
        );
    }
}
