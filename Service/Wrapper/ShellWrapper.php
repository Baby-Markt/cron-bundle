<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Wrapper;

/**
 * A simple exec wrapper to make testing with depending classes easier.
 * @package BabymarktExt\CronBundle\Helper
 */
class ShellWrapper implements ShellWrapperInterface
{

    /**
     * Output lines as an array.
     * @var array
     */
    protected $output;

    /**
     * Return status.
     * @var int
     */
    protected $errorCode;

    /**
     * @see http://php.net/manual/de/function.exec.php
     * @param string $command
     * @return string The last output line.
     */
    public function execute($command)
    {
        return exec($command, $this->output, $this->errorCode);
    }

    /**
     * Returns all output lines as an array.
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Returns output as a single string.
     * @return string
     */
    public function getOutputString()
    {
        return trim(implode(PHP_EOL, $this->output));
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return boolean TRUE if the error code is not 0, otherwise FALSE.
     */
    public function isFailed()
    {
        return $this->errorCode != 0;
    }
}