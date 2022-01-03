<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Reader;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;

/**
 * Class CrontabReader
 * @package Babymarkt\Symfony\CronBundle\Helper\CrontabEditor
 */
interface CrontabReaderInterface
{
    /**
     * Reads content from system crontab of the current user.
     * @throws AccessDeniedException if access to crontab is denied.
     * @return string[]
     */
    public function read(): array;
}
