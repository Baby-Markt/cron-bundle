<?php

namespace BabymarktExt\CronBundle\Tests\Fixtures;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Service\Writer\CrontabWriterInterface;

/**
 * Class BufferedWriter
 * @package BabymarktExt\CronBundle\Tests\Fixtures
 */
class BufferedWriter implements CrontabWriterInterface
{

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * Writes the given content to the system crontab of the current user.
     * @param string|array $lines
     * @throws AccessDeniedException if access to crontab is denied.
     */
    public function write(array $lines): void
    {
        $this->buffer += $lines;
    }

    /**
     * @return array
     */
    public function getClean(): array
    {
        $buffer       = $this->buffer;
        $this->buffer = [];

        return $buffer;
    }
}
