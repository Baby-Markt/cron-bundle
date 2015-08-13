<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Reader;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapperInterface;

/**
 * Read entries from system crontab.
 * @package BabymarktExt\CronBundle\Helper\CrontabEditor
 */
class CrontabReader implements CrontabReaderInterface
{

    /**
     * Default configuration.
     * @var array
     */
    protected $defaultConfig = [
        'bin'  => '/usr/bin/crontab',
        'user' => null,
        'sudo' => false
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
     * CrontabReader constructor.
     * @param ShellWrapperInterface $shell
     * @param array $config
     */
    public function __construct(ShellWrapperInterface $shell, array $config = [])
    {
        $this->shell = $shell;
        $this->setConfig($config);
    }

    /**
     * Reads content from system crontab of the current user.
     * @throws AccessDeniedException if access to crontab is denied.
     * @return string
     */
    public function read()
    {
        $result = $this->shell->execute($this->getCronCommand() . ' -l');

        if ($this->shell->isFailed()) {
            throw new AccessDeniedException($result, $this->shell->getErrorCode());
        }

        return $this->shell->getOutput();
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
     * @codeCoverageIgnore
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
        $this->config = array_replace($this->defaultConfig, (array)$config);
    }
}