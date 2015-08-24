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
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ValidateCommandTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;

    const SERVICE_DEF_CHECKER = 'babymarkt_ext_cron.service.definitionchecker';

    /**
     * @param ContainerBuilder $container
     * @return CommandTester
     */
    protected function getTester(ContainerBuilder $container)
    {
        $cmd = new ValidateCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:validate'));

        return $tester;
    }

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
            'crons' => [
                'test' => ['command' => 'some:command']
            ]
        ];

        $container = $this->getContainer($config);
        $container->set(self::SERVICE_DEF_CHECKER, $checkerStub);

        $tester = $this->getTester($container);
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertContains('test', $tester->getDisplay());
        $this->assertContains('some:command', $tester->getDisplay());
        $this->assertContains('OK', $tester->getDisplay());
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
            'crons' => [
                'test' => ['command' => 'some:command']
            ]
        ];

        $container = $this->getContainer($config);
        $container->set(self::SERVICE_DEF_CHECKER, $checkerStub);

        $tester = $this->getTester($container);
        $tester->execute([]);

        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertContains('test', $tester->getDisplay());
        $this->assertContains('some:command', $tester->getDisplay());
        $this->assertContains(DefinitionChecker::RESULT_INCORRECT_COMMAND, $tester->getDisplay());
    }

    public function testNoDefinitionsFound()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->never())->method('check');
        $checkerStub->expects($this->never())->method('getResult');

        $container = $this->getContainer();
        $container->set(self::SERVICE_DEF_CHECKER, $checkerStub);

        $tester = $this->getTester($container);
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertContains('No cron job definitions found', $tester->getDisplay());

    }
}
