<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Listener;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Entity\Report\Execution;
use BabymarktExt\CronBundle\Service\CronReport;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Listen for console command events and create a cron execution entry.
 * @package BabymarktExt\CronBundle\Service\Listener
 */
class ExecutionReportListener
{

    /**
     * Execution start time.
     * @var int
     */
    protected $start;

    /**
     * @var \DateTime
     */
    protected $startDatetime;

    /**
     * @var int
     */
    protected $duration;

    /**
     * The cron alias
     * @var string
     */
    protected $alias;

    /**
     * Cron's definition
     * @var Definition[]
     */
    protected $definitions;

    /**
     * @var bool
     */
    protected $skipped = false;

    /**
     * @var CronReport
     */
    protected $reporter;

    /**
     * @var Execution
     */
    protected $execution;

    /**
     * ExecutionReportListener constructor.
     * @param array $definitions
     * @param CronReport $reporter
     */
    public function __construct(array $definitions, CronReport $reporter)
    {
        $this->definitions   = $definitions;
        $this->reporter      = $reporter;
        $this->startDatetime = new \DateTime();
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onCronStart(ConsoleCommandEvent $event)
    {
        if (!$this->isCronCommand($event->getCommand()->getName(), $event->getInput())) {
            $this->skipped = true;
            return;
        }

        $this->start = microtime(true);
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onCronFinished(ConsoleTerminateEvent $event)
    {
        if (!$this->skipped) {
            $this->duration  = microtime(true) - $this->start;
            $this->execution = $this->reporter->logExecution($this->alias, $this->duration, $this->startDatetime);
        }
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function onCronFailed(ConsoleExceptionEvent $event)
    {
        if (!$this->skipped) {
            $this->execution->setFailed(true);
        }
    }


    /**
     * Checks if the given command is used by a cron definition.
     * @param string $command
     * @return bool
     */
    protected function isCronCommand($command, InputInterface $input)
    {
        foreach ($this->definitions as $alias => $def) {
            if ($def->getCommand()) {
                if ($command == $def->getCommand()) {
                    $this->alias = $alias;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @codeCoverageIgnore
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Returns true if the current command is skipped because its not a cron triggered call.
     * @codeCoverageIgnore
     * @return bool
     */
    public function isSkipped()
    {
        return $this->skipped;
    }

    /**
     * @codeCoverageIgnore
     * @return \BabymarktExt\CronBundle\Entity\Cron\Definition[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }


}