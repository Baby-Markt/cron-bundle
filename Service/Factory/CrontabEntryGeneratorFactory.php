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
use BabymarktExt\CronBundle\Service\CrontabEntryGenerator;

/**
 * Class CrontabEntryGeneratorFactory
 * @package BabymarktExt\CronBundle\Service\Factory
 */
class CrontabEntryGeneratorFactory
{

    /**
     * Cron configuration
     * @var Definition[]
     */
    protected $definitions;

    /**
     * Basedir of the console script.
     * @var string
     */
    protected $basedir;

    /**
     * @var string
     */
    protected $outputOptions;

    /**
     * @var string
     */
    protected $environment;

    /**
     * ListGenerator constructor.
     * @param array $definitions
     * @param array $outputOptions
     * @param string $basedir
     * @param string $environment
     */
    public function __construct(array $definitions, array $outputOptions, $basedir, $environment)
    {
        $this->definitions   = $definitions;
        $this->outputOptions = $outputOptions;
        $this->basedir       = realpath($basedir) ?: $basedir;
        $this->environment   = $environment;
    }

    /**
     * @return CrontabEntryGenerator
     */
    public function create()
    {
        $definitions = [];

        foreach ($this->definitions as $alias => $properties) {
            $definitions[$alias] = new Definition($properties);
        }

        return new CrontabEntryGenerator($definitions, $this->outputOptions, $this->basedir, $this->environment);
    }

}
