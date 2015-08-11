<?php

namespace BabymarktExt\CronBundle\Tests\Listener;

use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Service\Listener\ExecutionReportListener;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 05.08.15
 * Time: 14:49
 */
class ExecutionReportListenerTest extends \PHPUnit_Framework_TestCase
{

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

    protected $input;
    protected $output;
    protected $command;

    /**
     * @var CronReport
     */
    protected $reporter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->command = $this->getMockBuilder(ContainerAwareCommand::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command->method('getName')->willReturn('babymarkt:bundle:command');

        $this->output = new BufferedOutput();
        $this->input  = new ArgvInput();

        $this->reporter = $this->getMockBuilder(CronReport::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testUnknownCronCommand()
    {
        $reporter = new ExecutionReportListener([
            'cron1' => array_merge($this->definitionDefaults, ['command' => 'babymarkt:test:command'])
        ], $this->reporter);

        $reporter->onCronStart(new ConsoleCommandEvent($this->command, $this->input, $this->output));
        $this->assertTrue($reporter->isSkipped());
    }

    public function testCronDuration()
    {
        $reporter = new ExecutionReportListener([
            'cron1' => array_merge($this->definitionDefaults, ['command' => 'babymarkt:bundle:command'])
        ], $this->reporter);

        $reporter->onCronStart(new ConsoleCommandEvent($this->command, $this->input, $this->output));
        $reporter->onCronFinished(new ConsoleTerminateEvent($this->command, $this->input, $this->output, 0));

        $this->assertGreaterThan(0, $reporter->getDuration());
    }

}
