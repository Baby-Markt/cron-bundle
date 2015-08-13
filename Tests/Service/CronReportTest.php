<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Service;

use BabymarktExt\CronBundle\Entity\Report\Execution;
use BabymarktExt\CronBundle\Entity\Report\ExecutionRepository;
use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CronReportTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;

    /**
     * @var CronReport
     */
    protected $report;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityManager;

    /**
     * @var ExecutionRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    protected $definitionDefaults = [
        'minutes'   => '*',
        'hours'     => '*',
        'days'      => '*',
        'months'    => '*',
        'weekdays'  => '*',
        'enabled'   => true,
        'output'    => null,
        'command'   => null,
        'arguments' => []
    ];

    public function testEmFlushing()
    {
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Destroy the report instance.
        $this->report = null;
    }

    public function testLogExecution()
    {
        $testExec = new Execution();
        $testExec->setAlias('def1')
            ->setEnv($this->container->getParameter('kernel.environment'))
            ->setExecutionTime(1)
            ->setFailed(false)
            ->setExecutionDatetime(new \DateTime());

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Execution::class))
            ->willReturnCallback(function (Execution $execution) use ($testExec) {
                $this->assertEquals($execution, $testExec);
            });

        $this->report->logExecution(
            $testExec->getAlias(),
            $testExec->getExecutionTime(),
            $testExec->getExecutionDatetime(),
            $testExec->isFailed()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown cron alias "unknown"
     */
    public function testLogExecutionWithUnknownAlias()
    {
        $this->report->logExecution('unknown', 1, new \DateTime());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown cron alias "unknown"
     */
    public function testAliasReportWithUnknownAlias()
    {
        $this->report->createAliasReport('unknown');
    }

    public function testAliasReport()
    {
        $this->repository->expects($this->once())
            ->method('createReportByAlias')
            ->with($this->equalTo('def1'), $this->isNull());

        $result = $this->report->createAliasReport('def1');

        $this->assertCount(2, $result);
    }

    public function testEnvironmentReportCreation()
    {
        $result = $this->report->createEnvironmentReport(
            $this->container->getParameter('kernel.environment')
        );

        $this->assertCount(3, $result);
        $this->assertEquals('babymarkt:test:command1', $result[0]['command']);
        $this->assertEquals('-', $result[2]['command']);

        $result = $this->report->createEnvironmentReport('other');

        $this->assertCount(0, $result);
    }

    public function testClearingStats()
    {
        $this->repository->expects($this->once())
            ->method('deleteByEnvironment')
            ->with($this->equalTo($this->container->getParameter('kernel.environment')));

        $this->report->clearStats();
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // Now, mock the repository so it returns the mock of the employee
        $repo = $this
            ->getMockBuilder(ExecutionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();


//        $repo->method('createReportByEnvironment')->will($this->returnValue('test'));
//        var_dump($repo->createReportByEnvironment());

        $repo->method('createReportByEnvironment')->will($this->returnValueMap([
            [
                'test',
                [
                    [
                        'alias'                     => 'def1',
                        'exec_count'                => '12',
                        'avg_exec_time'             => '2',
                        'min_exec_time'             => '1',
                        'max_exec_time'             => '3',
                        'total_exec_time'           => '24',
                        'last_exec_datetime'        => '2015-08-07 15:46:12',
                        'exec_count_failed'         => '1',
                        'last_exec_datetime_failed' => '2015-08-07 15:46:12'
                    ],
                    [
                        'alias'                     => 'def2',
                        'exec_count'                => '12',
                        'avg_exec_time'             => '2',
                        'min_exec_time'             => '1',
                        'max_exec_time'             => '3',
                        'total_exec_time'           => '24',
                        'last_exec_datetime'        => '2015-08-07 15:46:12',
                        'exec_count_failed'         => '1',
                        'last_exec_datetime_failed' => '2015-08-07 15:46:12'
                    ],
                    [
                        'alias'                     => 'def_unknown',
                        'exec_count'                => '12',
                        'avg_exec_time'             => '2',
                        'min_exec_time'             => '1',
                        'max_exec_time'             => '3',
                        'total_exec_time'           => '24',
                        'last_exec_datetime'        => '2015-08-07 15:46:12',
                        'exec_count_failed'         => '1',
                        'last_exec_datetime_failed' => '2015-08-07 15:46:12'
                    ]
                ]
            ],
            [
                'other',
                []
            ]

        ]));
        $repo->method('createReportByAlias')
            ->will($this->returnValue([
                ['alias' => 'def1', 'and' => 'some', 'other' => 'values'],
                ['alias' => 'def1', 'and' => 'some', 'other' => 'values']
            ]));

        // Last, mock the EntityManager to return the mock of the repository
        /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject $entityManager */
        $entityManager = $this
            ->getMockBuilder('\Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager
            ->method('getRepository')
            ->with($this->equalTo(Execution::class))
            ->will($this->returnValue($repo));

        $this->container = $this->getContainer(['crons' => [
            'def1' => ['command' => 'babymarkt:test:command1'],
            'def2' => ['command' => 'babymarkt:test:command2']
        ]]);

        $this->report = new CronReport(
            $entityManager,
            $this->container->getParameter('kernel.environment'),
            $this->container->getParameter('babymarkt_ext_cron.definitions')
        );

        $this->entityManager = $entityManager;
        $this->repository = $repo;
    }


}
