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
use BabymarktExt\CronBundle\Service\Factory\CronReportFactory;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class CronReportFactoryTest extends \PHPUnit_Framework_TestCase
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

        /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject $emStub */
        $emStub = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();

        $factory = new CronReportFactory(
            $emStub,
            $container->getParameter('kernel.environment'),
            $container->getParameter('babymarkt_ext_cron.definitions')
        );

        $generator = $factory->create();

        $this->assertInstanceOf(CronReport::class, $generator);
        $this->assertContainsOnlyInstancesOf(Definition::class, $generator->getDefinitions());
    }

}
