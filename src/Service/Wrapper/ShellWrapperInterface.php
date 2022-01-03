<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Wrapper;

interface ShellWrapperInterface
{
    /**
     * @see http://php.net/manual/de/function.exec.php
     * @param string $command
     * @return string
     */
    public function execute(string $command): string;

    /**
     * @return array
     */
    public function getOutput(): array;

    /**
     * @return int
     */
    public function getErrorCode(): int;

    /**
     * @return boolean TRUE if the error code is not 0, otherwise FALSE.
     */
    public function isFailed(): bool;
}
