<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;
use Babymarkt\Symfony\CronBundle\Service\CrontabEditor;
use Babymarkt\Symfony\CronBundle\Service\CrontabEntryGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt-cron:sync';
    protected static $defaultDescription = 'Syncs all configured cronjobs with crontab.';

    const
        STATUS_NOT_WRITABLE = 1,
        STATUS_ACCESS_DENIED = 2;

    protected ?CrontabEntryGenerator $cronEntryGenerator;
    protected ?CrontabEditor $crontabEditor;

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Generate the cron entries from definitions
        $entries = $this->cronEntryGenerator->generateEntries();

        $io = new SymfonyStyle($input, $output);

        try {
            $this->crontabEditor->injectCronjobs($entries);
            $io->success(count($entries) . ' cronjobs successfully synced.');

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
     * @param CrontabEntryGenerator $cronEntryGenerator
     */
    public function setCrontabEntryGenerator(CrontabEntryGenerator $cronEntryGenerator): void
    {
        $this->cronEntryGenerator = $cronEntryGenerator;
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
