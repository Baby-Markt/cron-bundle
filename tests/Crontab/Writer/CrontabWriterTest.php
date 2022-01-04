<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Crontab\Writer;

use Babymarkt\Symfony\CronBundle\Crontab\Writer\CrontabWriter;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Shell\ShellWrapper;
use Babymarkt\Symfony\CronBundle\Shell\ShellWrapperInterface;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\ContainerTrait;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\StaticsLoaderTrait;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamException;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CrontabWriterTest extends TestCase
{
    use StaticsLoaderTrait, ContainerTrait;

    const ROOT_DIR = '/root/dir';
    const ENVIRONMENT = 'test';

    protected ContainerBuilder $container;

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

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');

        $shell->method('execute')->willReturnCallback(function ($command) use ($lines) {
            // Last parameter is the temp file with lines to write.
            $file = substr($command, 6 + strrpos($command, 'vfs://'));

            $vfsContent = vfsStreamWrapper::getRoot()->getChild($file)->getContent();

            $this->assertEquals(implode(PHP_EOL, $lines), trim($vfsContent));
        });

        (new CrontabWriter($shell, $config))->write($lines);
    }

    /**
     * @param ShellWrapperInterface $shell
     * @param ContainerBuilder $container
     * @param array $lines
     * @dataProvider commandGenerationData
     */
    public function testCommandGeneration(ShellWrapperInterface $shell, ContainerBuilder $container, array $lines)
    {
        $config = $container->getParameter('babymarkt_cron.options.crontab');

        (new CrontabWriter($shell, $config))->write($lines);
    }

    public function commandGenerationData(): \Generator
    {
        // The container has to be cloned because each getShell() call creates a new instance with different settings.
        yield [
            $this->getShell([], false, 0, true),
            $this->container,
            []
        ];
        yield [
            $this->getShell(['sudo' => true]),
            $this->container,
            ['test']
        ];
        yield [
            $this->getShell(['user' => 'testuser']),
            $this->container,
            ['test']
        ];
        yield [
            $this->getShell(['bin' => '/check/this/out/crontab']),
            $this->container,
            ['test']
        ];
    }

    public function testAccessDenied()
    {
        $this->expectExceptionCode(1);
        $this->expectException(AccessDeniedException::class);
        $shell = $this->getShell([], true, 1);

        $config = $this->container->getParameter('babymarkt_cron.options.crontab');
        $lines  = ['test'];

        (new CrontabWriter($shell, $config))->write($lines);
    }

    public function testNotWritable()
    {
        $this->expectException(WriteException::class);
        $this->expectExceptionMessageMatches("/is not writable.$/");
        $container = $this->getContainer([
            'options' => ['crontab' => ['tmpPath' => vfsStream::url('testpath')]]
        ]);

        /** @var vfsStreamDirectory $root */
        $root = vfsStreamWrapper::getRoot();
        $root->chmod(0444);

        $config = $container->getParameter('babymarkt_cron.options.crontab');
        $lines  = ['test'];

        (new CrontabWriter(new ShellWrapper(), $config))->write($lines);
    }

    /**
     * @param array $crontabConfig
     * @param bool $failed
     * @param int $errorCode
     * @param bool $empty
     * @return ShellWrapperInterface|MockObject
     */
    protected function getShell(array $crontabConfig = [], bool $failed = false, int $errorCode = 0, bool $empty = false)
    {
        $containerConfig = [
            'options' => ['crontab' => array_replace(['tmpPath' => vfsStream::url('testpath')], $crontabConfig)]
        ];

        $this->container = $this->getContainer($containerConfig);
        $config          = $this->container->getParameter('babymarkt_cron.options.crontab');

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
     * @throws vfsStreamException
     */
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testpath'));
    }


}
