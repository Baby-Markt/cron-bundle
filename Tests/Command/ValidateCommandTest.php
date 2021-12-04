<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Command;


use BabymarktExt\CronBundle\Command\ValidateCommand;
use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Service\DefinitionChecker;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ValidateCommandTest extends TestCase
{
    use ContainerTrait;

    public function testValidDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->once())
            ->method('check')
            ->with($this->isInstanceOf(Definition::class))
            ->willReturn(true);

        $checkerStub->expects($this->never())->method('getResult');

        $config = [
            'cronjobs' => [
                'test' => ['command' => 'some:command']
            ]
        ];
        $container = $this->getContainer($config);

        $cmd = new ValidateCommand();
        $cmd->setContainer($container);
        $cmd->setDefinitionChecker($checkerStub);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString('OK', $tester->getDisplay());
    }

    public function testInvalidDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->once())
            ->method('check')
            ->with($this->isInstanceOf(Definition::class))
            ->willReturn(false);

        $checkerStub->expects($this->once())
            ->method('getResult')
            ->willReturn(DefinitionChecker::RESULT_INCORRECT_COMMAND);

        $config = [
            'cronjobs' => [
                'test' => ['command' => 'some:command']
            ]
        ];

        $container = $this->getContainer($config);

        $cmd = new ValidateCommand();
        $cmd->setContainer($container);
        $cmd->setDefinitionChecker($checkerStub);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:validate'));
        $tester->execute([]);

        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString(DefinitionChecker::RESULT_INCORRECT_COMMAND, $tester->getDisplay());
    }

    public function testDisabledDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->never())
            ->method('check');

        $checkerStub->expects($this->never())
            ->method('getResult');

        $config = [
            'cronjobs' => [
                'test' => ['command' => 'some:command', 'disabled' => true]
            ]
        ];

        $container = $this->getContainer($config);

        $cmd = new ValidateCommand();
        $cmd->setContainer($container);
        $cmd->setDefinitionChecker($checkerStub);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString('Disabled', $tester->getDisplay());
    }

    public function testNoDefinitionsFound()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->never())->method('check');
        $checkerStub->expects($this->never())->method('getResult');

        $cmd = new ValidateCommand();
        $cmd->setContainer($this->getContainer());
        $cmd->setDefinitionChecker($checkerStub);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('No cron job definitions found', $tester->getDisplay());

    }
}
