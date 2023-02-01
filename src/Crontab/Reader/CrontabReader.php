<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab\Reader;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Shell\ShellWrapperInterface;

/**
 * Read entries from system crontab.
 */
class CrontabReader implements CrontabReaderInterface
{

    /**
     * Default configuration.
     * @var array
     */
    protected array $defaultConfig = [
        'bin'  => '/usr/bin/crontab',
        'user' => null,
        'sudo' => false
    ];

    /**
     * Custom configuration.
     * @var array
     */
    protected array $config = [];

    /**
     * @var ShellWrapperInterface
     */
    protected ShellWrapperInterface $shellWrapper;

    /**
     * CrontabReader constructor.
     * @param ShellWrapperInterface $shellWrapper
     * @param array $config
     */
    public function __construct(ShellWrapperInterface $shellWrapper, array $config = [])
    {
        $this->shellWrapper = $shellWrapper;
        $this->setConfig($config);
    }

    /**
     * Reads content from system crontab of the current user.
     * @return string[]
     * @throws AccessDeniedException if access to crontab is denied.
     */
    public function read(): array
    {
        $result = $this->shellWrapper->execute($this->getCronCommand() . ' -l');

        if ($this->shellWrapper->isFailed()) {
            // If failed but message is 'no crontab for ...', it's fine to return 0 rows.
            if (str_starts_with(trim(strtolower($this->shellWrapper->getErrorOutput()??"")), 'no crontab for')) {
                return [];
            }

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
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * return $this
     */
    public function setConfig(array $config)
    {
        $this->config = array_replace($this->defaultConfig, $config);
    }

}
