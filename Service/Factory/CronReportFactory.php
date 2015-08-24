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
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CronReportFactory
 * @package BabymarktExt\CronBundle\Service\Factory
 */
class CronReportFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var Definition[]
     */
    protected $definitions;

    /**
     * ReportWriter constructor.
     * @param EntityManagerInterface $em
     * @param string $environment
     * @param array $definitions
     */
    public function __construct(EntityManagerInterface $em, $environment, array $definitions)
    {
        $this->em          = $em;
        $this->environment = $environment;
        $this->definitions = $definitions;
    }

    /**
     * @return CronReport
     */
    public function create()
    {
        $definitions = [];

        foreach ($this->definitions as $alias => $properties) {
            $definitions[$alias] = new Definition($properties);
        }

        return new CronReport($this->em, $this->environment, $definitions);
    }
}