<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Symfony\Component\Console\Application;


/**
 * Checks the given definition against some simple rules.
 */
class DefinitionChecker
{
    const RESULT_INCORRECT_COMMAND = 'incorrectCommand';

    protected Application $application;
    protected ?string $result = null;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * Checks a single definition.
     */
    public function check(Definition $definition): bool
    {
        try {
            $this->application->find($definition->getCommand());
        } catch (\InvalidArgumentException $e) {
            $this->result = self::RESULT_INCORRECT_COMMAND;
            return false;
        }

        return true;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getApplication(): Application
    {
        return $this->application;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setApplication(Application $application): DefinitionChecker
    {
        $this->application = $application;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setResult(string $result): DefinitionChecker
    {
        $this->result = $result;
        return $this;
    }

}
