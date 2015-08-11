<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Entity\Report;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Execution
 * @package BabymarktExt\CronBundle\Model\Report\Execution
 *
 * @Entity(repositoryClass="BabymarktExt\CronBundle\Entity\Report\ExecutionRepository")
 * @Table(name="babymarkt_ext_cron.executions")
 */
class Execution
{

    /**
     * @var int
     * @Id() @Column(type="integer")
     * @GeneratedValue()
     */
    protected $id;

    /**
     * Environment
     * @var string
     * @Column()
     */
    protected $env;

    /**
     * Cron alias.
     * @var string
     * @Column()
     */
    protected $alias;

    /**
     * @var float
     * @Column(type="float", name="execution_time")
     */
    protected $executionTime;

    /**
     * @var \DateTime
     * @Column(type="datetime", name="execution_datetime")
     */
    protected $executionDatetime;

    /**
     * Execution failed.
     * @var bool
     * @Column(type="boolean", options={"default" = false})
     */
    protected $failed = false;

    /**
     * Execution constructor.
     */
    public function __construct()
    {
        $this->executionDatetime = new \DateTime();
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string $env
     * @return $this
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * @param float $executionTime
     * @return $this
     */
    public function setExecutionTime($executionTime)
    {
        $this->executionTime = $executionTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getExecutionDatetime()
    {
        return $this->executionDatetime;
    }

    /**
     * @return boolean
     */
    public function isFailed()
    {
        return $this->failed;
    }

    /**
     * @param boolean $failed
     * @return $this
     */
    public function setFailed($failed)
    {
        $this->failed = (bool)$failed;
        return $this;
    }

    /**
     * @param \DateTime $executionDatetime
     * @return $this
     */
    public function setExecutionDatetime($executionDatetime)
    {
        $this->executionDatetime = $executionDatetime;
        return $this;
    }


}