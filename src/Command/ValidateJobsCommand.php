<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Babymarkt\Symfony\CronBundle\Service\DefinitionChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt-cron:validate';
    protected static $defaultDescription = 'Validates the configured cron jobs against some simple rules.';

    protected ?DefinitionChecker $definitionChecker = null;
    protected array $definitions = [];

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->definitionChecker->setApplication($this->getApplication());

        $errorFound = false;
        $io         = new SymfonyStyle($input, $output);

        if (count((array)$this->definitions)) {
            $resultList = [];

            ksort($this->definitions);

            foreach ($this->definitions as $alias => $definitionData) {
                $definition = new Definition($definitionData);

                if ($definition->isDisabled()) {
                    $resultList[] = [
                        'alias'   => $alias,
                        'command' => $definition->getCommand(),
                        'result'  => '<comment>Disabled</comment>'
                    ];
                } else {
                    if (!$this->definitionChecker->check($definition)) {
                        $resultList[] = [
                            'alias'   => $alias,
                            'command' => $definition->getCommand(),
                            'result'  => '<error>' . $this->definitionChecker->getResult() . '</error>'
                        ];
                        $errorFound   = true;
                    } else {
                        $resultList[] = [
                            'alias'   => $alias,
                            'command' => $definition->getCommand(),
                            'result'  => '<info>OK</info>'
                        ];
                    }
                }
            }

            $io->createTable()
                ->setHeaders(['Alias', 'Command', 'Result'])
                ->setRows($resultList)
                ->render();

        } else {
            $io->info('No cron job definitions found.');
        }

        return (int)$errorFound;
    }

    /**
     * @required
     * @param DefinitionChecker $definitionChecker
     */
    public function setDefinitionChecker(DefinitionChecker $definitionChecker): void
    {
        $this->definitionChecker = $definitionChecker;
    }

    /**
     * @param array $definitions
     * @required
     */
    public function setDefinitions(array $definitions): void
    {
        $this->definitions = $definitions;
    }
}
