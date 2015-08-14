<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Command;

use BabymarktExt\CronBundle\Service\CronReport;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReportCommand
 * @package BabymarktExt\CronBundle\Command
 */
class ReportCommand extends ContainerAwareCommand
{
    const
        STATUS_DOCTRINE_REQUIRED = 1,
        STATUS_INVALID_ARGUMENT = 2;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('babymarktext:cron:report')
            ->setDescription('Shows a short report about cron executions.')
            ->addArgument('alias', InputArgument::OPTIONAL, 'The alias from cron definition.')
            ->addOption('json', 'j', InputOption::VALUE_NONE, 'Exports the report as json.')
            ->addOption('clear', 'c', InputOption::VALUE_NONE, 'Clear report data for current environment.');
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->getParameter('babymarkt_ext_cron.report.enabled')) {
            $output->writeln('<error>DoctrineBundle is required for cron execution reports.</error>');
            return self::STATUS_DOCTRINE_REQUIRED;
        }

        $alias = $input->getArgument('alias');
        $clear = $input->getOption('clear');

        if ($clear) {
            $this->printClearStats($output);

        } else {
            if ($alias) {
                return $this->printAliasReport($input, $output);

            } else {
                $this->printEnvironmentReport($input, $output);
            }
        }
    }

    /**
     * Clears the report stats.
     * @param OutputInterface $output
     */
    protected function printClearStats(OutputInterface $output)
    {
        /** @var CronReport $report */
        $report = $this->getContainer()->get('babymarkt_ext_cron.service.executionreporter');

        $result = $report->clearStats();

        if ($result > 0) {
            if (1 == $result) {
                $output->writeln('<info>1 record for environment "' . $report->getEnvironment() . '" successfully cleared!</info>');

            } else {
                $output->writeln('<info>' . $result . ' records for environment "' . $report->getEnvironment() . '" successfully cleared!</info>');
            }
        } else {
            $this->printNoRecords($output, $report->getEnvironment());
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int status code
     */
    protected function printAliasReport(InputInterface $input, OutputInterface $output)
    {
        /** @var CronReport $report */
        $report = $this->getContainer()->get('babymarkt_ext_cron.service.executionreporter');

        $alias = $input->getArgument('alias');
        try {
            $result = $report->createAliasReport($alias, 20);

            if ($input->getOption('json')) {
                $output->writeln(json_encode($result));

            } else {
                if (count($result)) {
                    $table = new Table($output);
                    $table->setHeaders(['Datetime', 'Execution time', 'Environment', 'Failed']);
                    $table->addRows($result);
                    $table->render();
                } else {
                    $this->printNoRecords($output);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::STATUS_INVALID_ARGUMENT;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function printEnvironmentReport(InputInterface $input, OutputInterface $output)
    {
        /** @var CronReport $report */
        $report = $this->getContainer()->get('babymarkt_ext_cron.service.executionreporter');

        $result = $report->createEnvironmentReport();

        if ($input->getOption('json')) {
            $output->writeln(json_encode($result));

        } else {
            if (count($result)) {
                $table = new Table($output);
                $table->setHeaders([
                    'Alias', 'Command', 'Count', 'Avg', 'Min', 'Max', 'Total', 'Last run', 'Failed count', 'Last failed'
                ]);
                $table->addRows($result);
                $table->render();
            } else {
                $this->printNoRecords($output, $report->getEnvironment());
            }
        }
    }

    /**
     * Writes a "no records found" message to output.
     * @param OutputInterface $output
     * @param null $env
     */
    protected function printNoRecords(OutputInterface $output, $env = null)
    {
        if ($env) {
            $output->writeln('<comment>No records for environment "' . $env . '" found.</comment>');
        } else {
            $output->writeln('<comment>No records found.</comment>');
        }
    }
}