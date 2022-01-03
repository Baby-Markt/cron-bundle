<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab;

use Babymarkt\Symfony\CronBundle\Crontab\Reader\CrontabReaderInterface;
use Babymarkt\Symfony\CronBundle\Crontab\Writer\CrontabWriterInterface;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;

/**
 * The crontab editor provides capabilities to manage cronjobs in the system crontab.
 */
class CrontabEditor
{

    /**
     * A string identifies a application cronjobs block within the crontab.
     * @var string
     */
    protected string $identifier;

    /**
     * @var CrontabReaderInterface
     */
    protected CrontabReaderInterface $reader;

    /**
     * @var CrontabWriterInterface
     */
    protected CrontabWriterInterface $writer;

    /**
     * @param string $identifier Crontab block identifier.
     * @param CrontabReaderInterface $reader Crontab reader
     * @param CrontabWriterInterface $writer Crontab writer
     */
    public function __construct(string $identifier, CrontabReaderInterface $reader, CrontabWriterInterface $writer)
    {
        $this->identifier = $identifier;
        $this->reader     = $reader;
        $this->writer     = $writer;
    }

    /**
     * Injects the cron entries into crontab.
     * @param array $cronjobs
     * @throws AccessDeniedException if access to crontab is prohibited.
     * @throws WriteException if the temp path is not writable.
     * @todo Add a simple locking mechanism.
     */
    public function injectCronjobs(array $cronjobs): void
    {
        // Read current crontab contents
        $crontab = $this->reader->read();

        // Remove old cron entries.
        $crontab = $this->purgeOldCronjobs($crontab);

        // Add the new cronjobs block to crontab lines.
        $crontab[] = $this->generateStartLine();
        $crontab   += $cronjobs;
        $crontab[] = $this->generateEndLine();

        // Write content back into crontab.
        $this->writer->write($crontab);
    }

    /**
     * Removes the existing cronjobs from crontab.
     * @throws AccessDeniedException if access to crontab is prohibited.
     * @todo Add a simple locking mechanism.
     */
    public function removeCronjobs(): void
    {
        $crontab = $this->reader->read();
        $crontab = $this->purgeOldCronjobs($crontab);

        $this->writer->write($crontab);
    }

    /**
     * Removes the old cronjobs with same cronjobs block identifier.
     */
    protected function purgeOldCronjobs($crontab): array
    {
        $start = $this->generateStartLine();
        $end   = $this->generateEndLine();

        $cleanedCrontab = [];
        $inBlock        = false;

        foreach ($crontab as $line) {
            if (!empty($line) && strpos($start, $line) !== false) {
                $inBlock = true;
            }

            if (!$inBlock) {
                $cleanedCrontab[] = $line;
            }

            if (!empty($line) && strpos($end, $line) !== false) {
                $inBlock = false;
            }
        }

        return $cleanedCrontab;
    }

    /**
     * Generates the block start line.
     * @return string
     */
    protected function generateStartLine(): string
    {
        return sprintf('###> %s ###', $this->identifier);
    }

    /**
     * Generates the block end line.
     * @return string
     */
    protected function generateEndLine(): string
    {
        return sprintf('###< %s ###', $this->identifier);
    }
}
