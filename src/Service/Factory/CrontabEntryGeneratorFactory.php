<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Factory;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Babymarkt\Symfony\CronBundle\Service\CrontabEntryGenerator;

/**
 * Class CrontabEntryGeneratorFactory
 * @package Babymarkt\Symfony\CronBundle\Service\Factory
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
     * @param string $basedir
     * @param string $environment
     */
    public function __construct(array $definitions, array $outputOptions, string $basedir, string $environment)
    {
        $this->definitions   = $definitions;
        $this->outputOptions = $outputOptions;
        $this->basedir       = realpath($basedir) ?: $basedir;
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
