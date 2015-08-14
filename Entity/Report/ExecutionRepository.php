<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Entity\Report;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

/**
 * Class Repository
 * @package BabymarktExt\CronBundle\Model\Report\Execution
 * @codeCoverageIgnore
 */
class ExecutionRepository extends EntityRepository
{

    /**
     * @param string $environment
     * @return Execution[]
     */
    public function getByEnvironment($environment)
    {
        $result = $this->getEntityManager()
            ->createQuery('SELECT e FROM BabymarktCronBundle:Report\Execution e WHERE e.env = :env ORDER BY e.alias')
            ->setParameter('env', $environment)
            ->getResult();

        return $result;
    }

    /**
     * Deletes all stats from given environment.
     * @param $environment
     * @return int Affected objects count.
     */
    public function deleteByEnvironment($environment)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $result = $qb
            ->delete('BabymarktCronBundle:Report\Execution', 'e')
            ->andWhere($qb->expr()->eq('e.env', ':env'))
            ->setParameter('env', $environment)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * Returns the latest x executions.
     * @param string $alias
     * @param null $limit
     * @return Execution[]
     */
    public function createReportByAlias($alias, $limit = null)
    {
        $result = $this->getEntityManager()
            ->createQuery('
              SELECT e.executionDatetime, e.executionTime, e.env, e.failed
              FROM BabymarktCronBundle:Report\Execution e
              WHERE e.alias = :alias
              ORDER BY e.executionDatetime DESC')
            ->setParameter('alias', $alias)
            ->setMaxResults($limit)
            ->getResult(AbstractQuery::HYDRATE_SCALAR);

        return array_reverse($result);
    }

    /**
     * @param string $environment
     * @return array
     */
    public function createReportByEnvironment($environment)
    {

        $result = $this->getEntityManager()
            ->createQuery('
                SELECT
                  e.alias,
                  COUNT(e) as exec_count,
                  AVG(e.executionTime) as avg_exec_time,
                  MIN(e.executionTime) as min_exec_time,
                  MAX(e.executionTime) as max_exec_time,
                  SUM(e.executionTime) as total_exec_time,
                  MAX(e.executionDatetime) as last_exec_datetime,
                  (SELECT COUNT(ef1) FROM BabymarktCronBundle:Report\Execution ef1 WHERE ef1.failed = true AND ef1.alias = e.alias) as exec_count_failed,
                  (SELECT MAX(ef2.executionDatetime) FROM BabymarktCronBundle:Report\Execution ef2 WHERE ef2.failed = true AND ef2.alias = e.alias) as last_exec_datetime_failed
                FROM BabymarktCronBundle:Report\Execution e WHERE e.env = :env AND e.failed = false
                GROUP BY e.alias
                ORDER BY e.alias
            ')
            ->setParameter('env', $environment)
            ->getResult();

        return $result;
    }

}