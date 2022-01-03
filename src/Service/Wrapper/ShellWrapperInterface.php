<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Wrapper;

/**
 * Class ExecWrapper
 * @package Babymarkt\Symfony\CronBundle\Helper
 */
interface ShellWrapperInterface
{
    /**
     * @see http://php.net/manual/de/function.exec.php
     * @param string $command
     * @return string
     */
    public function execute($command);

    /**
     * @return array
     */
    public function getOutput();

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return boolean TRUE if the error code is not 0, otherwise FALSE.
     */
    public function isFailed();
}
