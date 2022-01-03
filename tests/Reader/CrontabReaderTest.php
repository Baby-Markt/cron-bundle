<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\Tests\Reader;

use Babymarkt\Symfony\CronBundle\DependencyInjection\BabymarktCronExtension;
use Babymarkt\Symfony\CronBundle\Service\Reader\CrontabReader;
use Babymarkt\Symfony\CronBundle\Service\Wrapper\ShellWrapperInterface;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\StaticsLoaderTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CrontabReaderTest
 * @package Babymarkt\Symfony\CronBundle\Tests\Reader
 */
class CrontabReaderTest extends TestCase
{

    use StaticsLoaderTrait;

    const ROOT_DIR = '/root/dir';
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
        $shell = $this->getShell([]);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $reader = new CrontabReader($config);
        $reader->setShellWrapper($shell);
        $result = $reader->read();

        $this->assertEquals($this->loadStaticFixture('crontab.txt'), implode(PHP_EOL, $result));
    }

    public function testReadWillFail()
    {
        $this->expectExceptionCode(1);
        $this->expectException(\Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException::class);
        $shell = $this->getShell([], true, 1);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $reader = new CrontabReader($config);
        $reader->setShellWrapper($shell);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithSudo()
    {
        $shell = $this->getShell(['sudo' => true]);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $reader = new CrontabReader($config);
        $reader->setShellWrapper($shell);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithUser()
    {
        $shell = $this->getShell(['user' => 'testuser']);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $reader = new CrontabReader($config);
        $reader->setShellWrapper($shell);
        $reader->read();
    }

    /**
     * The check is defined in the shell mock as a matcher parameter.
     */
    public function testWithDifferentBinPath()
    {
        $shell = $this->getShell(['bin' => '/check/this/out/crontab']);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $reader = new CrontabReader($config);
        $reader->setShellWrapper($shell);
        $reader->read();
    }

    /**
     * @param array $crontabConfig
     * @param bool $failed
     * @param int $errorCode
     * @return ShellWrapperInterface|MockObject
     */
    protected function getShell(array $crontabConfig, bool $failed = false, int $errorCode = 0)
    {
        $containerConfig = [
            'options' => ['crontab' => $crontabConfig]
        ];

        $this->container = $this->getContainer($containerConfig);
        $config          = $this->container->getParameter('babymarkt_cron.options.crontab');

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
    protected function getContainer(array $config = []): ContainerBuilder
    {
        $ext  = new BabymarktCronExtension();
        $cont = new ContainerBuilder();
        $cont->setParameter('kernel.bundles', []);
        $cont->setParameter('kernel.project_dir', self::ROOT_DIR);
        $cont->setParameter('kernel.environment', self::ENVIRONMENT);

        $ext->load([$config], $cont);

        return $cont;
    }


}
