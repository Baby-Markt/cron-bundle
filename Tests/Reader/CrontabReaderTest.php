<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Reader;

use BabymarktExt\CronBundle\DependencyInjection\BabymarktExtCronExtension;
use BabymarktExt\CronBundle\Service\Reader\CrontabReader;
use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapperInterface;
use BabymarktExt\CronBundle\Tests\Fixtures\StaticsLoaderTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CrontabReaderTest
 * @package BabymarktExt\CronBundle\Tests\Reader
 */
class CrontabReaderTest extends \PHPUnit_Framework_TestCase
{

    use StaticsLoaderTrait;

    const ROOT_DIR    = '/root/dir';
    const ENVIRONMENT = 'test';


    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function testReadFromCrontabWithDefaultConfig()
    {
        $shell = $this->getShell();

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $reader = new CrontabReader($shell, $config);
        $result = $reader->read();

        $this->assertEquals($this->loadStaticFixture('crontab.txt'), implode(PHP_EOL, $result));
    }

    /**
     * @expectedException \BabymarktExt\CronBundle\Exception\AccessDeniedException
     * @expectedExceptionCode 1
     */
    public function testReadWillFail()
    {
        $shell = $this->getShell([], true, 1);

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $reader = new CrontabReader($shell, $config);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithSudo()
    {
        $shell = $this->getShell(['sudo' => true]);

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $reader = new CrontabReader($shell, $config);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithUser()
    {
        $shell = $this->getShell(['user' => 'testuser']);

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $reader = new CrontabReader($shell, $config);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithDifferentBinPath()
    {
        $shell = $this->getShell(['bin' => '/check/this/out/crontab']);

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $reader = new CrontabReader($shell, $config);
        $reader->read();
    }

    /**
     * @param array $crontabConfig
     * @param bool $failed
     * @param int $errorCode
     * @return ShellWrapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getShell(array $crontabConfig = [], $failed = false, $errorCode = 0)
    {
        $containerConfig = [
            'options' => ['crontab' => $crontabConfig]
        ];

        $this->container = $this->getContainer($containerConfig);
        $config          = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $crontab = $this->loadStaticFixture('crontab.txt');
        $shell   = $this->getMockBuilder(ShellWrapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = $config['bin'];

        if ($config['sudo']) {
            $command = 'sudo ' . $command;
        }

        if ($config['user']) {
            $command .= ' -u ' . $config['user'];
        }

        $command .= ' -l';

        // Execute returns the last line from command output
        $shell->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($command))
            ->will($this->returnValue(substr($crontab, 1 + strrpos($crontab, PHP_EOL))));

        $shell->expects($failed ? $this->never() : $this->once())
            ->method('getOutput')
            ->willReturn(explode(PHP_EOL, $crontab));

        $shell->expects($this->once())
            ->method('isFailed')
            ->willReturn($failed);

        $shell->method('getErrorCode')->willReturn($errorCode);

        return $shell;
    }

    /**
     * @param array $config
     * @return ContainerBuilder
     */
    protected function getContainer($config = [])
    {
        $ext  = new BabymarktExtCronExtension();
        $cont = new ContainerBuilder();
        $cont->setParameter('kernel.bundles', []);
        $cont->setParameter('kernel.root_dir', self::ROOT_DIR);
        $cont->setParameter('kernel.environment', self::ENVIRONMENT);

        $ext->load([$config], $cont);

        return $cont;
    }




}
