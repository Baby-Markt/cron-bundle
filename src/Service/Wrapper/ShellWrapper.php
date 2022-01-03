<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Wrapper;

/**
 * A simple exec wrapper to make testing with depending classes easier.
 * @package Babymarkt\Symfony\CronBundle\Helper
 */
class ShellWrapper implements ShellWrapperInterface
{

    /**
     * Output lines of the command execution.
     */
    protected array $output = [];

    /**
     * Return status.
     */
    protected int $errorCode = 0;

    /**
     * @see http://php.net/manual/de/function.exec.php
     * @param string $command
     * @return string The last output line.
     */
    public function execute(string $command): string
    {
        return exec($command, $this->output, $this->errorCode);
    }

    /**
     * Returns all output lines as an array.
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * Returns output as a single string.
     * @return string
     */
    public function getOutputString(): string
    {
        return trim(implode(PHP_EOL, $this->output));
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return boolean TRUE if the error code is not 0, otherwise FALSE.
     */
    public function isFailed(): bool
    {
        return $this->errorCode != 0;
    }
}
