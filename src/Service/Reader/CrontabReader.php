<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Reader;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Service\Wrapper\ShellWrapperInterface;

/**
 * Read entries from system crontab.
 * @package Babymarkt\Symfony\CronBundle\Helper\CrontabEditor
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
    protected $shellWrapper;

    /**
     * CrontabReader constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Reads content from system crontab of the current user.
     * @throws AccessDeniedException if access to crontab is denied.
     * @return string[]
     */
    public function read(): array
    {
        $result = $this->shellWrapper->execute($this->getCronCommand() . ' -l');

        if ($this->shellWrapper->isFailed()) {
            throw new AccessDeniedException($result, $this->shellWrapper->getErrorCode());
        }

        return $this->shellWrapper->getOutput();
    }

    /**
     * Returns the cron command.
     */
    protected function getCronCommand(): string
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

    /**
     * @required
     * @param ShellWrapperInterface $shellWrapper
     */
    public function setShellWrapper(ShellWrapperInterface $shellWrapper): void
    {
        $this->shellWrapper = $shellWrapper;
    }

}
