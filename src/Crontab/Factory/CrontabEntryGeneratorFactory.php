<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab\Factory;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEntryGenerator;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;

/**
 * A factory class to create an instance of the crontab entry generator.
 * @see CrontabEntryGenerator
 */
class CrontabEntryGeneratorFactory
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
     * @var string
     */
    protected $outputOptions;

    /**
     * @var string
     */
    protected string $environment;

    /**
     * ListGenerator constructor.
     * @param array $definitions
     * @param array $outputOptions
     * @param string $baseDir
     * @param string $environment
     */
    public function __construct(array $definitions, array $outputOptions, string $baseDir, string $environment)
    {
        $this->definitions   = $definitions;
        $this->outputOptions = $outputOptions;
        $this->basedir       = realpath($baseDir) ?: $baseDir;
        $this->environment   = $environment;
    }

    /**
     * @return CrontabEntryGenerator
     */
    public function create(): CrontabEntryGenerator
    {
        $definitions = [];

        foreach ($this->definitions as $alias => $properties) {
            $definitions[$alias] = new Definition($properties);
        }

        return new CrontabEntryGenerator($definitions, $this->outputOptions, $this->basedir, $this->environment);
    }

}
