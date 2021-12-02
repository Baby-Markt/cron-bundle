<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Writer;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;

/**
 * Class CrontabWriter
 * @package BabymarktExt\CronBundle\Helper\CrontabEditor
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
