<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Command;

use BabymarktExt\CronBundle\Service\CronEntryGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 04.08.15
 * Time: 13:53
 */
class DumpCommand extends Command
{
    /**
     * @var CronEntryGenerator
     */
    protected $cronEntryGenerator;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('babymarktext:cron:dump')
            ->setDescription('Shows a list of all configured crons.');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $listEntries = $this->cronEntryGenerator->generateEntries();

        foreach ($listEntries as $entry) {
            $output->writeln($entry);
        }

        return 0;
    }

    /**
     * @required
     * @param CronEntryGenerator $cronEntryGenerator
     */
    public function setCronEntryGenerator(CronEntryGenerator $cronEntryGenerator): void
    {
        $this->cronEntryGenerator = $cronEntryGenerator;
    }

}
