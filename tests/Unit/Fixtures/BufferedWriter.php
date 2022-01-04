<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Unit\Fixtures;

use Babymarkt\Symfony\CronBundle\Crontab\Writer\CrontabWriterInterface;
use Babymarkt\Symfony\CronBundle\Exception\AccessDeniedException;

class BufferedWriter implements CrontabWriterInterface
{

    protected array $buffer = [];

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
