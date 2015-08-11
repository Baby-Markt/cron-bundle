<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Reader;
use BabymarktExt\CronBundle\Exception\AccessDeniedException;

/**
 * Class CrontabReader
 * @package BabymarktExt\CronBundle\Helper\CrontabEditor
 */
interface CrontabReaderInterface
{
    /**
     * Reads content from system crontab of the current user.
     * @throws AccessDeniedException if access to crontab is denied.
     * @return string
     */
    public function read();
}