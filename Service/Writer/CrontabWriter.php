<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Writer;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;
use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapperInterface;

/**
 * Write new entries back to system crontab.
 * @package BabymarktExt\CronBundle\Helper\CrontabEditor
 */
class CrontabWriter implements CrontabWriterInterface
{

    /**
     * Default configuration.
     * @var array
     */
    protected $defaultConfig = [
        'tmpPath' => '/tmp',
        'bin'     => '/usr/bin/crontab',
        'user'    => null,
        'sudo'    => false
    ];

    /**
     * Custom configuration.
     * @var array
     */
    protected $config = [];

    /**
     * @var ShellWrapperInterface
     */
    protected $shell;

    /**
     * CrontabWriter constructor.
     * @param ShellWrapperInterface $shell
     * @param array $config
     */
    public function __construct(ShellWrapperInterface $shell, array $config = [])
    {
        $this->shell = $shell;
        $this->setConfig($config);
    }

    /**
     * Writes the given content to the system crontab of the current user.
     *
     * @param array $lines
     * @throws AccessDeniedException if access to crontab is denied.
     * @throws WriteException if the temp path is not writable.
     */
    public function write(array $lines)
    {
        // Reindex content array to ensure numeric indexes.
        $lines = array_values($lines);

        if (($lineCount = count($lines)) > 0) {

            // Add a new line to last line to prevent the error:
            // "new crontab file is missing newline before EOF, can't install.".
            $lines[$lineCount - 1] = rtrim($lines[$lineCount - 1]) . PHP_EOL;

            $tmpFile = $this->config['tmpPath'] . DIRECTORY_SEPARATOR . uniqid() . '.cron.import';

            if (!is_writable($this->config['tmpPath'])) {
                throw new WriteException('Temp path "' . $this->config['tmpPath'] . '" is not writable.');
            }

            // Write lines to file.
            file_put_contents($tmpFile, implode(PHP_EOL, (array)$lines));

            $result = $this->shell->execute($this->getCronCommand() . ' ' . $tmpFile);

            unlink($tmpFile);

        } else {
            // If no lines should be added to crontab, clear crontab for current user.
            $result = $this->shell->execute($this->getCronCommand() . ' -r');
        }

        if ($this->shell->isFailed()) {
            throw new AccessDeniedException($result, $this->shell->getErrorCode());
        }
    }

    /**
     * Returns the cron command.
     */
    protected function getCronCommand()
    {
        if ($this->config['sudo']) {
            $command = 'sudo ' . $this->config['bin'];
        } else {
            $command = $this->config['bin'];
        }

        if ($this->config['user']) {
            $command .= ' -u ' . $this->config['user'];
        }

        return $command;
    }

    /**
     * @return array
     * @codeConverageIgnore
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * return $this
     */
    public function setConfig($config)
    {
        $this->config = array_replace($this->defaultConfig, $config);
    }
}