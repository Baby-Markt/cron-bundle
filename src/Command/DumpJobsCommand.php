<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Command;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEntryGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DumpJobsCommand extends Command
{
    protected static $defaultName = 'babymarkt-cron:dump';
    protected static $defaultDescription = 'Shows a list of all configured cronjobs.';

    protected CrontabEntryGenerator $cronEntryGenerator;

    public function __construct(CrontabEntryGenerator $cronEntryGenerator)
    {
        $this->cronEntryGenerator = $cronEntryGenerator;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $listEntries = $this->cronEntryGenerator->generateEntries();

        foreach ($listEntries as $entry) {
            $output->writeln($entry);
        }

        return 0;
    }

}
