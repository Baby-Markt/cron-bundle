<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Command;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Service\DefinitionChecker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This silly test command is only for checking the cron report listener.
 * @package BabymarktExt\CronBundle\Command
 */
class ValidateCommand extends Command
{
    /**
     * @var DefinitionChecker
     */
    protected $definitionChecker;

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('babymarktext:cron:validate')
            ->setDescription('Validates the configured cron jobs against some simple rules.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->definitionChecker->setApplication($this->getApplication());

        $errorFound = false;

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


            $table = new Table($output);
            $table->setHeaders([
                'Alias', 'Command', 'Result'
            ]);
            $table->setRows($resultList);
            $table->render();

        } else {
            $output->writeln('<comment>No cron job definitions found.</comment>');
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
