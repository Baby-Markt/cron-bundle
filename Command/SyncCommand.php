<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Command;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;
use BabymarktExt\CronBundle\Service\CronEntryGenerator;
use BabymarktExt\CronBundle\Service\CrontabEditor;
use BabymarktExt\CronBundle\Service\Writer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: nfunke
 * Date: 04.08.15
 * Time: 13:53
 */
class SyncCommand extends ContainerAwareCommand
{
    const
        STATUS_NOT_WRITABLE = 1,
        STATUS_ACCESS_DENIED = 2;


    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('babymarktext:cron:sync')
            ->setDescription('Syncs all configured crons with crontab.');
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
        /** @var CronEntryGenerator $generator */
        $generator = $this->getContainer()->get('babymarkt_ext_cron.service.cronentrygenerator');

        /** @var CrontabEditor $editor */
        $editor = $this->getContainer()->get('babymarkt_ext_cron.service.crontabeditor');

        // Generate the cron entries from definitions
        $entries = $generator->generateEntries();

        try {
            $editor->injectCrons($entries);
            $output->writeln('<info>' . count($entries) . ' crons successfully synced.</info>');

        } catch (WriteException $e) {
            $output->writeln('<error>Can\'t write to crontab.</error>');
            $output->writeln($e->getMessage());
            return self::STATUS_NOT_WRITABLE;

        } catch (AccessDeniedException $e) {
            $output->writeln('<error>Can\'t access crontab.</error>');
            $output->writeln($e->getMessage());
            return self::STATUS_ACCESS_DENIED;
        }
    }
}