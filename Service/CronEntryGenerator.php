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

/**
 * Class ListGenerator
 * @package BabymarktExt\CronBundle\DependencyInjection
 */
class CronEntryGenerator
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
     * @var string
     */
    protected $script = 'bin/console';

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
     * @return array
     */
    public function generateEntries()
    {
        $entries = [];

        foreach ($this->definitions as $alias => $def) {
            if (!$def->isDisabled()) {
                $entry   = [];
                $entry[] = $this->getIntervalString(
                    $def->getMinutes(),
                    $def->getHours(),
                    $def->getDays(),
                    $def->getMonths(),
                    $def->getWeekdays()
                );
                $entry[] = $this->getCommandString($def);
                $entry[] = $this->getArgumentsString($def->getArguments());
                $entry[] = $this->getOutputString($def->getOutput());

                $entries[$alias] = implode(' ', array_filter($entry));
            }
        }

        return $entries;
    }

    /**
     * Generates the arguments string.
     * @param array $arguments
     * @return string
     */
    protected function getArgumentsString(array $arguments)
    {
        if (count($arguments) > 0) {
            return implode(' ', $arguments);
        }
        return '';
    }

    /**
     * Returns the interval definition string.
     * @param string|int $minutes
     * @param string|int $hours
     * @param string|int $days
     * @param string|int $months
     * @param string|int $weekdays
     * @return string
     */
    protected function getIntervalString($minutes, $hours, $days, $months, $weekdays)
    {
        return implode(' ', [$minutes, $hours, $days, $months, $weekdays]);
    }

    /**
     * Returns the output redirection string.
     * @param string $output
     * @return string
     */
    protected function getOutputString($output = null)
    {
        $file   = $output['file'] ?: $this->outputOptions['file'];
        $append = $output['append'] ?: $this->outputOptions['append'];

        if ($append) {
            return sprintf('2>&1 1>>%s', $file);
        } else {
            return sprintf('2>&1 1>%s', $file);
        }
    }

    /**
     * Returns the command part.
     * @param Definition $def
     * @return string
     */
    protected function getCommandString(Definition $def)
    {
        if (empty($def->getCommand())) {
            throw new \InvalidArgumentException('Cron command is required.');
        }

        return vsprintf('cd %s; php %s --env=%s %s', [
            $this->basedir,
            $this->script,
            $this->environment,
            $def->getCommand()
        ]);
    }

    /**
     * @codeCoverageIgnore
     * @return Definition[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @codeCoverageIgnore
     * @param Definition[] $definitions
     * @return $this
     */
    public function setDefinitions(array $definitions)
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getBasedir()
    {
        return $this->basedir;
    }

    /**
     * @codeCoverageIgnore
     * @param string $basedir
     * @return $this
     */
    public function setBasedir($basedir)
    {
        $this->basedir = realpath($basedir) ?: $basedir;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getOutputOptions()
    {
        return $this->outputOptions;
    }

    /**
     * @codeCoverageIgnore
     * @param string $outputOptions
     * @return $this
     */
    public function setOutputOptions($outputOptions)
    {
        $this->outputOptions = $outputOptions;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @codeCoverageIgnore
     * @param string $environment
     * @return $this
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @param string $script
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }
}
