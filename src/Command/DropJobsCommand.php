<?php

declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Service\CrontabEditor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DropJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt_cron:drop';
    protected static $defaultDescription = 'Drops all configured cronjobs from crontab.';

    const
        STATUS_NOT_WRITABLE = 1,
        STATUS_ACCESS_DENIED = 2;

    protected ?CrontabEditor $crontabEditor;

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->crontabEditor->removeCronjobs();
            $io->info('All cronjobs successfully dropped.');

        } catch (WriteException $e) {
            $io->error('Can\'t write to crontab.');
            $output->writeln($e->getMessage());
            return self::STATUS_NOT_WRITABLE;

        } catch (AccessDeniedException $e) {
            $io->error('Can\'t access crontab.');
            $output->writeln($e->getMessage());
            return self::STATUS_ACCESS_DENIED;
        }

        return 0;
    }

    /**
     * @required
     * @param CrontabEditor $crontabEditor
     */
    public function setCrontabEditor(CrontabEditor $crontabEditor): void
    {
        $this->crontabEditor = $crontabEditor;
    }

}
