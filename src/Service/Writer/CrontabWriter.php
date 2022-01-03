<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Writer;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Service\Wrapper\ShellWrapperInterface;

/**
 * Write new entries back to system crontab.
 * @package Babymarkt\Symfony\CronBundle\Helper\CrontabEditor
 */
class CrontabWriter implements CrontabWriterInterface
{

    /**
     * Default configuration.
     * @var array
     */
    protected array $defaultConfig = [
        'tmpPath' => '/tmp',
        'bin'     => '/usr/bin/crontab',
        'user'    => null,
        'sudo'    => false
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
     * CrontabWriter constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Writes the given content to the system crontab of the current user.
     *
     * @param array $lines
     * @throws AccessDeniedException if access to crontab is denied.
     * @throws WriteException if the temp path is not writable.
     */
    public function write(array $lines): void
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
            file_put_contents($tmpFile, implode(PHP_EOL, $lines));

            $result = $this->shellWrapper->execute($this->getCronCommand() . ' ' . $tmpFile);

            unlink($tmpFile);

        } else {
            // If no lines should be added to crontab, clear crontab for current user.
            $result = $this->shellWrapper->execute($this->getCronCommand() . ' -r');
        }

        if ($this->shellWrapper->isFailed()) {
            throw new AccessDeniedException($result, $this->shellWrapper->getErrorCode());
        }
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
     * @return array|null
     * @codeCoverageIgnore
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * return $this
     */
    public function setConfig(array $config): void
    {
        $this->config = array_replace($this->defaultConfig, $config);
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
