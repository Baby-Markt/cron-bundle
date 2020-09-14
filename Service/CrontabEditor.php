<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service;

use BabymarktExt\CronBundle\Exception\AccessDeniedException;
use BabymarktExt\CronBundle\Exception\WriteException;
use BabymarktExt\CronBundle\Service\Reader\CrontabReaderInterface;
use BabymarktExt\CronBundle\Service\Writer\CrontabWriterInterface;

/**
 * The crontab editor provides the capabilities to manage cron's in the system crontab.
 * @package BabymarktExt\CronBundle\Service
 */
class CrontabEditor
{

    /**
     * A string identifies a application cron's block within the crontab.
     * @var string
     */
    protected $identifier;

    /**
     * @var CrontabReaderInterface
     */
    protected $reader;

    /**
     * @var CrontabWriterInterface
     */
    protected $writer;

    /**
     * CrontabEditor constructor.
     *
     * @param string $identifier Crontab block identifier.
     * @param CrontabReaderInterface $reader
     * @param CrontabWriterInterface $writer
     */
    public function __construct($identifier, CrontabReaderInterface $reader, CrontabWriterInterface $writer)
    {
        $this->identifier = $identifier;
        $this->reader     = $reader;
        $this->writer     = $writer;
    }

    /**
     * Injects the cron entries into crontab.
     * @param array $crons
     * @todo Add a simple locking mechanism.
     * @throws AccessDeniedException if access to crontab is prohibited.
     * @throws WriteException if the temp path is not writable.
     */
    public function injectCrons(array $crons)
    {
        // Read current crontab contents
        $crontab = $this->reader->read();

        // Remove old cron entries.
        $crontab = $this->purgeOldCrons($crontab);

        // Add the new cron's block to crontab lines.
        $crontab[] = $this->generateStartLine();
        $crontab += $crons;
        $crontab[] = $this->generateEndLine();

        // Write content back into crontab.
        $this->writer->write($crontab);
    }

    /**
     * Removes the existing crons from crontab.
     * @todo Add a simple locking mechanism.
     * @throws AccessDeniedException if access to crontab is prohibited.
     */
    public function removeCrons()
    {
        $crontab = $this->reader->read();
        $crontab = $this->purgeOldCrons($crontab);

        $this->writer->write($crontab);
    }

    /**
     * Removes the old crons with same cron's block identifier.
     */
    protected function purgeOldCrons($crontab)
    {
        $start = $this->generateStartLine();
        $end   = $this->generateEndLine();

        $cleanedCrontab = [];
        $inBlock        = false;

        foreach ($crontab as $line) {
            if (!empty($line) && strpos($start, $line) !== false) {
                $inBlock = true;
            }

            if (!$inBlock) {
                $cleanedCrontab[] = $line;
            }

            if (!empty($line) && strpos($end, $line) !== false) {
                $inBlock = false;
            }
        }

        return $cleanedCrontab;
    }

    /**
     * Generates the block start line.
     * @return string
     */
    protected function generateStartLine()
    {
        return sprintf('### CRONTAB-EDITOR-START %s ###', $this->identifier);
    }

    /**
     * Generates the block end line.
     * @return string
     */
    protected function generateEndLine()
    {
        return sprintf('### CRONTAB-EDITOR-END %s ###', $this->identifier);
    }


}