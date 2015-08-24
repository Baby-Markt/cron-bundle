<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Service\Factory;


use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Service\Factory\ExecutionReportListenerFactory;
use BabymarktExt\CronBundle\Service\Listener\ExecutionReportListener;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;

class ExecutionReportListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;


    public function testFactory()
    {
        $config = [
            'crons' => [
                'test-job' => [
                    'command'  => 'babymarktext:test:command'
                ]
            ]
        ];

        $container = $this->getContainer($config);

        /** @var CronReport|\PHPUnit_Framework_MockObject_MockObject $reportStub */
        $reportStub = $this->getMockBuilder(CronReport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory = new ExecutionReportListenerFactory(
            $container->getParameter('babymarkt_ext_cron.definitions'),
            $reportStub
        );

        $generator = $factory->create();

        $this->assertInstanceOf(ExecutionReportListener::class, $generator);
        $this->assertContainsOnlyInstancesOf(Definition::class, $generator->getDefinitions());
    }
}
