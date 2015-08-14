<?php

namespace BabymarktExt\CronBundle\Tests\Listener;

use BabymarktExt\CronBundle\Entity\Report\Execution;
use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Service\Listener\ExecutionReportListener;
use BabymarktExt\CronBundle\Tests\Fixtures\ContainerTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 05.08.15
 * Time: 14:49
 */
class ExecutionReportListenerTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;

    /**
     * @var ArrayInput
     */
    protected $input;
    protected $output;
    protected $command;

    /**
     * @var CronReport
     */
    protected $reporter;

    /**
     * @var Execution
     */
    protected $execution;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->command = $this->getMockBuilder(ContainerAwareCommand::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command->method('getName')->willReturn('babymarktext:test:command');

        $this->output = new BufferedOutput();
        $this->input  = new ArrayInput([]);

        $this->reporter = $this->getMockBuilder(CronReport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->execution = new Execution();

        $this->reporter->method('logExecution')
            ->willReturn($this->execution);
    }

    public function testUnknownCronCommand()
    {
        $container = $this->getContainer([
            'crons' => [
                'def1' => [
                    'command' => 'babymarkt:unknown:command'
                ]
            ]
        ]);

        $reporter = new ExecutionReportListener(
            $container->getParameter('babymarkt_ext_cron.definitions'),
            $this->reporter
        );

        $reporter->onCronStart(new ConsoleCommandEvent($this->command, $this->input, $this->output));
        $this->assertTrue($reporter->isSkipped());

        $reporter->onCronFinished(new ConsoleTerminateEvent($this->command, $this->input, $this->output, 1));
        $this->assertEmpty($reporter->getDuration());

        $reporter->onCronFailed(
            new ConsoleExceptionEvent($this->command, $this->input, $this->output, new \Exception(), 1)
        );
        $this->assertFalse($this->execution->isFailed());
    }

    public function testCronFailed()
    {
        $container = $this->getContainer([
            'crons' => [
                'def1' => [
                    'command' => 'babymarktext:test:command'
                ]
            ]
        ]);

        $reporter = new ExecutionReportListener(
            $container->getParameter('babymarkt_ext_cron.definitions'),
            $this->reporter
        );

        $reporter->onCronStart(new ConsoleCommandEvent($this->command, $this->input, $this->output));
        $reporter->onCronFinished(new ConsoleTerminateEvent($this->command, $this->input, $this->output, 1));
        $reporter->onCronFailed(
            new ConsoleExceptionEvent($this->command, $this->input, $this->output, new \Exception(), 1)
        );

        $this->assertFalse($reporter->isSkipped());
        $this->assertTrue($this->execution->isFailed());
    }

    public function testCronDuration()
    {
        $container = $this->getContainer([
            'crons' => [
                'def1' => [
                    'command' => 'babymarktext:test:command'
                ]
            ]
        ]);

        $reporter = new ExecutionReportListener(
            $container->getParameter('babymarkt_ext_cron.definitions'),
            $this->reporter
        );

        $reporter->onCronStart(new ConsoleCommandEvent($this->command, $this->input, $this->output));
        $reporter->onCronFinished(new ConsoleTerminateEvent($this->command, $this->input, $this->output, 0));

        $this->assertGreaterThan(0, $reporter->getDuration());
    }

}
