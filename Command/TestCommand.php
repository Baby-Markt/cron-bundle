<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Command;

use BabymarktExt\CronBundle\Service\Writer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This silly test command is only for checking the cron report listener.
 * @package BabymarktExt\CronBundle\Command
 */
class TestCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('babymarktext:cron:test')
            ->setDescription('Goes for sleep for n seconds with a funny animation.')
            ->addArgument('seconds', InputArgument::OPTIONAL, 'Seconds to sleep', rand(1, 10));
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
        $sec = (int)$input->getArgument('seconds');
        $output->writeln('Going to bed for ' . $sec . ' sec.');
        $output->write('(-.-) ');
        for ($i = 0; $i < $sec * 4; $i++) {
            usleep(250000);
            $output->write($i % 2 ? 'Z' : 'z');
        }

        $output->write(PHP_EOL);
        $output->writeln('<error>(((O.o))) BAZINGAAA!</error>');
    }

}