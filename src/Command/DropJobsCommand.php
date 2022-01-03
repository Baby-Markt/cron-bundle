<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEditor;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DropJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt-cron:drop';
    protected static $defaultDescription = 'Drops all configured cronjobs from crontab.';

    const EXITCODE_NOT_WRITABLE = 1;
    const EXITCODE_ACCESS_DENIED = 2;

    protected CrontabEditor $crontabEditor;

    public function __construct(CrontabEditor $crontabEditor)
    {
        $this->crontabEditor = $crontabEditor;
        parent::__construct();
    }

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
            return self::EXITCODE_NOT_WRITABLE;

        } catch (AccessDeniedException $e) {
            $io->error('Can\'t access crontab.');
            $output->writeln($e->getMessage());
            return self::EXITCODE_ACCESS_DENIED;
        }

        return 0;
    }

}
