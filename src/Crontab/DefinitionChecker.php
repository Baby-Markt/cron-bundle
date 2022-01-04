<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab;

use Babymarkt\Symfony\CronBundle\DependencyInjection\Compiler\CommandDefinitionPass;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Symfony\Component\Console\Command\Command;

/**
 * Checks cronjob definitions against few simple rules.
 */
class DefinitionChecker
{
    const RESULT_COMMAND_NOT_FOUND = 'unknownCommand';

    /**
     * @var Command[]
     */
    protected array $commands = [];

    protected ?string $result = null;

    /**
     * Checks a single definition.
     */
    public function check(Definition $definition): bool
    {
        // Check if the command exists.
        foreach ($this->commands as $command) {
            if ($command->getName() == $definition->getCommand()) {
                return true;
            }
        }

        $this->result = self::RESULT_COMMAND_NOT_FOUND;
        return false;
    }

    /**
     * This is called by service definition.
     * @see CommandDefinitionPass
     * @param Command $command
     * @return void
     */
    public function addCommand(Command $command)
    {
        $this->commands[] = $command;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

}
