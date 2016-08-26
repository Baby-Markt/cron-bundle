<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Command;


use BabymarktExt\CronBundle\Command\ReportCommand;
use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ReportCommandTest
 * @package BabymarktExt\CronBundle\Tests\Command
 */
class ReportCommandTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait {
        getContainer as parentGetContainer;
    }

    const SERVICE_CRON_REPORT  = 'babymarkt_ext_cron.service.cronreport';
    const PARAM_REPORT_ENABLED = 'babymarkt_ext_cron.report.enabled';

    protected function getContainer($config = [])
    {
        $cont = $this->parentGetContainer($config);
        $cont->setParameter(self::PARAM_REPORT_ENABLED, true);

        $reporter = $this->getMockBuilder(CronReport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $reporter->method('getEnvironment')->willReturn('test');

        $cont->set(self::SERVICE_CRON_REPORT, $reporter);

        return $cont;
    }

    /**
     * @param ContainerBuilder $container
     * @return CommandTester
     */
    protected function getTester(ContainerBuilder $container)
    {
        $cmd = new ReportCommand();
        $cmd->setContainer($container);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarktext:cron:report'));

        return $tester;
    }

    public function testMissingDoctrineBundle()
    {
        $container = $this->getContainer();
        $container->setParameter(self::PARAM_REPORT_ENABLED, false);

        $tester = $this->getTester($container);
        $tester->execute([]);

        $this->assertContains('is disabled', $tester->getDisplay());
        $this->assertEquals(ReportCommand::STATUS_REPORTING_DISABLED, $tester->getStatusCode());
    }

    /**
     * @param array $returnValue
     * @param string $expectedDisplay
     * @param bool $jsonFormat
     * @dataProvider environmentReportData
     */
    public function testEnvironmentReport(array $returnValue, $expectedDisplay, $jsonFormat)
    {
        $container = $this->getContainer();

        /** @var \PHPUnit_Framework_MockObject_MockObject $reporter */
        $reporter = $container->get(self::SERVICE_CRON_REPORT);
        $reporter->expects($this->once())
            ->method('createEnvironmentReport')
            ->willReturn($returnValue);

        $tester = $this->getTester($container);
        $tester->execute(['--json' => $jsonFormat]);

        foreach ((array)$expectedDisplay as $expectedString){
            $this->assertContains($expectedString, $tester->getDisplay());
        }
    }

    /**
     * @param $count
     * @param $expectedString
     * @dataProvider clearingStatsData
     */
    public function testClearingStats($count, $expectedString)
    {
        $container = $this->getContainer();

        /** @var \PHPUnit_Framework_MockObject_MockObject $reporter */
        $reporter = $container->get(self::SERVICE_CRON_REPORT);
        $reporter->expects($this->once())
            ->method('clearStats')
            ->willReturn($count);

        $tester = $this->getTester($container);
        $tester->execute(['--clear' => true]);

        $this->assertContains($expectedString, $tester->getDisplay());
    }

    /**
     * @param $returnValue
     * @param $expectedDisplay
     * @dataProvider aliasReportData
     */
    public function testAliasReport($returnValue, $expectedDisplay, $jsonFormat)
    {
        $container = $this->getContainer();

        /** @var \PHPUnit_Framework_MockObject_MockObject $reporter */
        $reporter = $container->get(self::SERVICE_CRON_REPORT);
        $reporter->expects($this->once())
            ->method('createAliasReport')
            ->with($this->equalTo('test_alias'), $this->isType('int'))
            ->willReturn($returnValue);

        $tester = $this->getTester($container);
        $tester->execute(['alias' => 'test_alias', '--json' => $jsonFormat]);

        foreach ((array)$expectedDisplay as $expected) {
            $this->assertContains($expected, $tester->getDisplay());
        }
    }

    public function testAliasReportWithInvalidArgument()
    {
        $container = $this->getContainer();

        /** @var \PHPUnit_Framework_MockObject_MockObject $reporter */
        $reporter = $container->get(self::SERVICE_CRON_REPORT);
        $reporter->expects($this->once())
            ->method('createAliasReport')
            ->willThrowException(new \InvalidArgumentException('test exception'));

        $tester = $this->getTester($container);
        $tester->execute(['alias' => 'test_alias']);

        $this->assertContains('test exception', $tester->getDisplay());
        $this->assertEquals(ReportCommand::STATUS_INVALID_ARGUMENT, $tester->getStatusCode());
    }

    public function clearingStatsData()
    {
        $env = $this->getContainer()->getParameter('kernel.environment');

        return [
            [1, '1 record for environment "' . $env . '"'],
            [2, '2 records for environment "' . $env . '"'],
            [0, 'No records for environment "' . $env . '" found']
        ];
    }

    public function aliasReportData()
    {
        return [
            [[], 'No records found.', false],
            [[], '[]', true], // Empty json string
            [[['field1', 'field2', 'field3', 'field4']], ['field1', 'field2', 'field3', 'field4'], false]
        ];
    }

    public function environmentReportData()
    {
        $env = $this->getContainer()->getParameter('kernel.environment');

        $returnValue = [
            [
                'alias'      => 'test_alias',
                'command'    => 'babymarktext:test:command',
                'count'      => 1,
                'avg'        => 1,
                'min'        => 1,
                'max'        => 1,
                'total'      => 1,
                'lastRun'    => '0000-00-00 00:00:00',
                'failCount'  => 1,
                'lastFailed' => '0000-00-00 00:00:00'

            ]
        ];

        return [
            [$returnValue, '{"alias":"test_alias","command":"babymarktext:test:command"', true],
            [$returnValue, ['test_alias', 'babymarktext:test:command', '0000-00-00 00:00:00'], false],
            [[], 'No records for environment "' . $env . '" found', false],
            [[], '[]', true]
        ];
    }
}
