<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

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
