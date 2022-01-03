<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Crontab\DefinitionChecker;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt-cron:validate';
    protected static $defaultDescription = 'Validates the configured cron jobs against some simple rules.';

    protected DefinitionChecker $definitionChecker;
    protected array $definitions;

    public function __construct(DefinitionChecker $definitionChecker, array $definitions)
    {
        $this->definitionChecker = $definitionChecker;
        $this->definitions       = $definitions;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->definitionChecker->setApplication($this->getApplication());

        $errorFound = false;
        $io         = new SymfonyStyle($input, $output);

        if (count($this->definitions)) {
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
}
