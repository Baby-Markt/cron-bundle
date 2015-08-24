<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service;

use BabymarktExt\CronBundle\Entity\Cron\Definition;
use BabymarktExt\CronBundle\Entity\Report\Execution;
use BabymarktExt\CronBundle\Entity\Report\ExecutionRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CronReporter
 * @package BabymarktExt\CronBundle\Service\Writer
 */
class CronReport
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var Definition[]
     */
    protected $definitions;

    /**
     * ReportWriter constructor.
     * @param EntityManagerInterface $em
     * @param string $environment
     * @param array $definitions
     */
    public function __construct(EntityManagerInterface $em, $environment, array $definitions)
    {
        $this->em          = $em;
        $this->environment = $environment;
        $this->definitions = $definitions;
    }

    /**
     * Flushes the entity manager.
     */
    public function __destruct()
    {
        $this->em->flush();
    }

    /**
     * Logs a new cron execution.
     *
     * @param string $alias
     * @param float $executionTime
     * @param \DateTime $startTime
     * @param bool $failed
     *
     * @return Execution
     */
    public function logExecution($alias, $executionTime, \DateTime $startTime, $failed = false)
    {
        if (!array_key_exists($alias, $this->definitions)) {
            throw new \InvalidArgumentException('Unknown cron alias "' . $alias . '"');
        }

        $execution = new Execution();
        $execution->setAlias($alias)
            ->setEnv($this->environment)
            ->setExecutionTime($executionTime)
            ->setExecutionDatetime($startTime)
            ->setFailed($failed);

        $this->em->persist($execution);

        return $execution;
    }

    /**
     * @param string $environment
     * @return array
     */
    public function createEnvironmentReport($environment = null)
    {
        /** @var ExecutionRepository $repo */
        $repo       = $this->em->getRepository(Execution::class);
        $execReport = $repo->createReportByEnvironment($environment ?: $this->environment);

        foreach ($execReport as &$exec) {
            $aliasPosition = array_search('alias', array_keys($exec)) + 1;

            if (isset($this->definitions[$exec['alias']])) {
                $command = $this->definitions[$exec['alias']]->getCommand();

            } else {
                // Cron definition is missing. Cannot read command.
                $command = '-';
            }

            // Add command entry after alias.
            $exec = array_slice($exec, 0, $aliasPosition, true)
                + ['command' => $command]
                + array_slice($exec, 1, count($exec) - 1, true);
        }

        return $execReport;

    }

    /**
     * Clears the stats for the current environment.
     * @return int Affected stats count.
     */
    public function clearStats()
    {
        /** @var ExecutionRepository $repo */
        $repo = $this->em->getRepository(Execution::class);
        return $repo->deleteByEnvironment($this->environment);
    }

    /**
     * @param string $alias
     * @param null $limit
     * @return array
     */
    public function createAliasReport($alias, $limit = null)
    {
        if (!array_key_exists($alias, $this->definitions)) {
            throw new \InvalidArgumentException('Unknown cron alias "' . $alias . '"');
        }

        /** @var ExecutionRepository $repo */
        $repo       = $this->em->getRepository(Execution::class);
        $execReport = $repo->createReportByAlias($alias, $limit);

        return $execReport;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param array $definitions
     * @return $this
     * @codeCoverageIgnore
     */
    public function setDefinitions(array $definitions)
    {
        $this->definitions = $definitions;
        return $this;
    }


}