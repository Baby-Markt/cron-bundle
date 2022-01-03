<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;

/**
 * Class ListGenerator
 */
class CrontabEntryGenerator
{

    /**
     * Cron configuration
     * @var Definition[]
     */
    protected array $definitions;

    /**
     * Basedir of the console script.
     * @var string
     */
    protected $basedir;

    /**
     * @var array
     */
    protected array $outputOptions;

    /**
     * @var string
     */
    protected string $environment;

    /**
     * @var string
     */
    protected string $script = 'bin/console';

    /**
     * ListGenerator constructor.
     * @param array $definitions
     * @param array $outputOptions
     * @param string $basedir
     * @param string $environment
     */
    public function __construct(array $definitions, array $outputOptions, string $basedir, string $environment)
    {
        $this->definitions   = $definitions;
        $this->outputOptions = $outputOptions;
        $this->basedir       = realpath($basedir) ?: $basedir;
        $this->environment   = $environment;

        ksort($this->definitions);
    }

    /**
     * Generates the cron entries.
     */
    public function generateEntries(): array
    {
        $entries = [];

        foreach ($this->definitions as $alias => $def) {
            $def->setAlias($alias);
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

                $entries[$alias] = $this->getCommentString($def) . PHP_EOL . implode(' ', array_filter($entry));
            }
        }

        return $entries;
    }

    protected function getCommentString(Definition $definition): string
    {
        return sprintf("# job '%s' (%s)",
            $definition->getAlias(),
            $definition->getDescription() ?? 'no description');
    }

    /**
     * Generates the arguments string.
     * @param array $arguments
     * @return string
     */
    protected function getArgumentsString(array $arguments): string
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
    protected function getIntervalString($minutes, $hours, $days, $months, $weekdays): string
    {
        return implode(' ', [$minutes, $hours, $days, $months, $weekdays]);
    }

    /**
     * Returns the output redirection string.
     */
    protected function getOutputString(array $output = null): string
    {
        $file   = $output['file'] ?? $this->outputOptions['file'];
        $append = $output['append'] ?? $this->outputOptions['append'];

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
    protected function getCommandString(Definition $def): string
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
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @codeCoverageIgnore
     * @param Definition[] $definitions
     * @return $this
     */
    public function setDefinitions(array $definitions): CrontabEntryGenerator
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getBasedir(): string
    {
        return $this->basedir;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setBasedir(string $basedir): CrontabEntryGenerator
    {
        $this->basedir = realpath($basedir) ?: $basedir;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getOutputOptions(): array
    {
        return $this->outputOptions;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setOutputOptions(array $outputOptions): CrontabEntryGenerator
    {
        $this->outputOptions = $outputOptions;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setEnvironment(string $environment): CrontabEntryGenerator
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getScript(): string
    {
        return $this->script;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setScript(string $script): void
    {
        $this->script = $script;
    }
}
