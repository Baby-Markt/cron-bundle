<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Crontab\Reader;

use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;

interface CrontabReaderInterface
{
    /**
     * Reads content from system crontab of the current user.
     * @return string[]
     * @throws AccessDeniedException if access to crontab is denied.
     */
    public function read(): array;
}
