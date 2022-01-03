<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Writer;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;
use Babymarkt\Symfony\CronBundle\Exception\WriteException;

/**
 * Class CrontabWriter
 * @package Babymarkt\Symfony\CronBundle\Helper\CrontabEditor
 */
interface CrontabWriterInterface
{
    /**
     * Writes the given content to the system crontab of the current user.
     * @param array $lines
     * @throws AccessDeniedException if access to crontab is denied.
     * @throws WriteException if the temp path is not writable.
     */
    public function write(array $lines): void;

}
