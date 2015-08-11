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
use Doctrine\ORM\EntityManagerInterface;

class CronReportTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CronReport
     */
    protected $report;

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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown cron alias "unknown"
     */
    public function testAliasReportWithUnknownAlias()
    {
        $this->report->createAliasReport('unknown');
    }

    public function testEnvironmentReportCreation()
    {
        $result = $this->report->createEnvironmentReport();

        $this->assertCount(2, $result);

        $result = $this->report->createEnvironmentReport('other');

        $this->assertCount(0, $result);
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
                ['alias' => 'def2', 'and' => 'some', 'other' => 'values']
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

        $this->report = new CronReport($entityManager, 'test', [
            'def1' => array_replace($this->definitionDefaults, ['command' => 'babymarkt:test:command']),
            'def2' => array_replace($this->definitionDefaults, ['command' => 'babymarkt:test:command'])
        ]);
    }


}
