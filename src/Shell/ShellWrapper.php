<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Shell;

/**
 * A simple wrapper to encapsulate command execution and make it easier to test classes that depend on it.
 */
class ShellWrapper implements ShellWrapperInterface
{

    /**
     * Output lines of the command execution.
     */
    protected array $stdOut = [];

    /**
     * Return status.
     */
    protected int $errorCode = 0;

    /**
     * Holds the standard error output.
     * @var string
     */
    protected string $stdErr;

    /**
     * @see http://php.net/manual/de/function.exec.php
     * @param string $command
     * @return string The last output line.
     */
    public function execute(string $command): string
    {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w"),  // stderr
        );
        $process = proc_open($command, $descriptorspec, $pipes);

        $this->stdOut = explode(PHP_EOL, stream_get_contents($pipes[1]));
        fclose($pipes[1]);

        $this->stdErr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $this->errorCode = proc_close($process);

        return $this->stdOut[0];
    }

    /**
     * Returns all output lines as an array.
     * @return array
     */
    public function getOutput(): array
    {
        return $this->stdOut;
    }

    /**
     * Returns output as a single string.
     * @return string
     */
    public function getOutputString(): string
    {
        return trim(implode(PHP_EOL, $this->stdOut));
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

    public function getErrorOutput(): ?string
    {
        return $this->stdErr;
    }
}
