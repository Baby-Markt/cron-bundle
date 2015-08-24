<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use Symfony\Component\Console\Application;


/**
 * Checks the given definition against some simple rules.
 * @package BabymarktExt\CronBundle\Service
 */
class DefinitionChecker
{
    const
        RESULT_INCORRECT_COMMAND = 'incorrectCommand';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $result;

    /**
     * Checks a single definition.
     *
     * @param Definition $definition
     * @return bool
     */
    public function check(Definition $definition)
    {

        try {
            $this->application->find($definition->getCommand());
        } catch (\InvalidArgumentException $e) {
            $this->result = self::RESULT_INCORRECT_COMMAND;
            return false;
        }

        return true;
    }

    /**
     * @codeCoverageIgnore
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @codeCoverageIgnore
     * @param Application $application
     * @return $this
     */
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @codeCoverageIgnore
     * @param string $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

}