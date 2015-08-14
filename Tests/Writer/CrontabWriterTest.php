<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Writer;

use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapper;
use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapperInterface;
use BabymarktExt\CronBundle\Service\Writer\CrontabWriter;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use BabymarktExt\CronBundle\Tests\Fixtures\StaticsLoaderTrait;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CrontabWriterTest
 * @package BabymarktExt\CronBundle\Tests\Writer
 */
class CrontabWriterTest extends \PHPUnit_Framework_TestCase
{
    use StaticsLoaderTrait, ContainerTrait;

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
        $lines = [
            'line 1', 'line 2', 'line 3'
        ];

        $shell = $this->getShell();

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $shell->method('execute')->willReturnCallback(function ($command) use ($lines) {
            // Last parameter is the temp file with lines to write.
            $file = substr($command, 6 + strrpos($command, 'vfs://'));

            /** @noinspection PhpUndefinedMethodInspection */
            $vfsContent = vfsStreamWrapper::getRoot()->getChild($file)->getContent();

            $this->assertEquals(implode(PHP_EOL, $lines), trim($vfsContent));
        });

        $writer = new CrontabWriter($shell, $config);
        $writer->write($lines);
    }

    /**
     * @param ShellWrapperInterface $shell
     * @param ContainerBuilder $container
     * @param $lines
     * @dataProvider commandGenerationData
     */
    public function testCommandGeneration(ShellWrapperInterface $shell, ContainerBuilder $container, array $lines)
    {
        $config = $container->getParameter('babymarkt_ext_cron.options.crontab');

        $writer = new CrontabWriter($shell, $config);
        $writer->write($lines);
    }

    public function commandGenerationData()
    {
        // The container has to be cloned because each getShell() call creates a new instance with different settings.
        return [
            [
                $this->getShell([], false, 0, true),
                clone $this->container,
                []
            ],
            [
                $this->getShell(['sudo' => true]),
                clone $this->container,
                ['test']
            ],
            [
                $this->getShell(['user' => 'testuser']),
                clone $this->container,
                ['test']
            ],
            [
                $this->getShell(['bin' => '/check/this/out/crontab']),
                clone $this->container,
                ['test']
            ]
        ];
    }

    /**
     * @expectedException \BabymarktExt\CronBundle\Exception\AccessDeniedException
     * @expectedExceptionCode 1
     */
    public function testAccessDenied()
    {
        $shell = $this->getShell([], true, 1);

        $config = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $writer = new CrontabWriter($shell, $config);
        $writer->write(['test']);
    }

    /**
     * @expectedException \BabymarktExt\CronBundle\Exception\WriteException
     * @expectedExceptionMessageRegExp /is not writable.$/
     */
    public function testNotWritable()
    {
        $container = $this->getContainer([
            'options' => ['crontab' => ['tmpPath' => vfsStream::url('testpath')]]
        ]);

        /** @var vfsStreamDirectory $root */
        $root = vfsStreamWrapper::getRoot();
        $root->chmod(0444);

        $config = $container->getParameter('babymarkt_ext_cron.options.crontab');

        $writer = new CrontabWriter(new ShellWrapper(), $config);
        $writer->write(['test']);
    }

    /**
     * @param array $crontabConfig
     * @param bool $failed
     * @param int $errorCode
     * @param bool $empty
     * @return ShellWrapperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getShell(array $crontabConfig = [], $failed = false, $errorCode = 0, $empty = false)
    {
        $containerConfig = [
            'options' => ['crontab' => array_replace(['tmpPath' => vfsStream::url('testpath')], $crontabConfig)]
        ];

        $this->container = $this->getContainer($containerConfig);
        $config          = $this->container->getParameter('babymarkt_ext_cron.options.crontab');

        $crontab = $this->loadStaticFixture('crontab.txt');
        $shell   = $this->getMockBuilder(ShellWrapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = $config['bin'];

        if ($empty) {
            $command .= ' -r';

        } else {
            if ($config['sudo']) {
                $command = 'sudo ' . $command;
            }

            if ($config['user']) {
                $command .= ' -u ' . $config['user'];
            }
        }

        // Execute returns the last line from command output
        $shell->expects($this->once())
            ->method('execute')
            ->with($empty ? $this->equalTo($command) : $this->stringStartsWith($command))
            ->will($this->returnValue(substr($crontab, 1 + strrpos($crontab, PHP_EOL))));

        $shell->expects($this->once())
            ->method('isFailed')
            ->willReturn($failed);

        $shell->method('getErrorCode')->willReturn($errorCode);

        return $shell;
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testpath'));
    }


}
