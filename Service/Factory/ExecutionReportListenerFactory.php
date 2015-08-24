<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Factory;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Service\CronReport;
use BabymarktExt\CronBundle\Service\Listener\ExecutionReportListener;

/**
 * Class ExecutionReportListenerFactory
 * @package BabymarktExt\CronBundle\Service\Factory
 */
class ExecutionReportListenerFactory
{
    /**
     * Cron's definition
     * @var Definition[]
     */
    protected $definitions;

    /**
     * @var CronReport
     */
    protected $reporter;

    /**
     * ExecutionReportListener constructor.
     * @param array $definitions
     * @param CronReport $reporter
     */
    public function __construct(array $definitions, CronReport $reporter)
    {
        $this->definitions = $definitions;
        $this->reporter    = $reporter;
    }

    /**
     * @return ExecutionReportListener
     */
    public function create()
    {
        $definitions = [];

        foreach ($this->definitions as $alias => $properties) {
            $definitions[$alias] = new Definition($properties);
        }

        return new ExecutionReportListener($definitions, $this->reporter);
    }
}