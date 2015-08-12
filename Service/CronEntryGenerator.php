<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service;

/**
 * Class ListGenerator
 * @package BabymarktExt\CronBundle\DependencyInjection
 */
class CronEntryGenerator
{

    /**
     * Cron configuration
     * @var array
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
     * @return array
     */
    public function generateEntries()
    {
        $entries = [];

        foreach ($this->definitions as $key => $def) {
            if ($def['enabled']) {
                $entry   = [];
                $entry[] = $this->getIntervalString(
                    $def['minutes'], $def['hours'], $def['days'], $def['months'], $def['weekdays']
                );
                $entry[] = $this->getCommandString($def);
                $entry[] = $this->getArgumentsString($def['arguments']);
                $entry[] = $this->getOutputString($def['output']);

                $entries[$key] = implode(' ', array_filter($entry));
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
     * @param string $def
     * @return string
     */
    protected function getCommandString($def)
    {
        if (!isset($def['command']) || !$def['command']) {
            throw new \InvalidArgumentException('Cron command is required.');
        }

        return vsprintf('cd %s; php console --env=%s %s', [
            $this->basedir,
            $this->environment,
            $def['command']
        ]);
    }

}