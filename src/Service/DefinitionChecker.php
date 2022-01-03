<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\Service;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Symfony\Component\Console\Application;


/**
 * Checks the given definition against some simple rules.
 * @package Babymarkt\Symfony\CronBundle\Service
 */
class DefinitionChecker
{
    const RESULT_INCORRECT_COMMAND = 'incorrectCommand';

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
     */
    public function check(Definition $definition): bool
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
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setApplication(Application $application): DefinitionChecker
    {
        $this->application = $application;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setResult(string $result): DefinitionChecker
    {
        $this->result = $result;
        return $this;
    }

}
