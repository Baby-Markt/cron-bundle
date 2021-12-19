<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Entity\Cron;

/**
 * A cron definition.
 * @package BabymarktExt\CronBundle\Entity
 */
class Definition
{
    /**
     * The job alias.
     * @var string
     */
    protected $alias;

    /**
     * Minutes.
     * @var string
     */
    protected $minutes = '*';

    /**
     * Hours.
     * @var string
     */
    protected $hours = '*';

    /**
     * Days.
     * @var string
     */
    protected $days = '*';

    /**
     * Months.
     * @var string
     */
    protected $months = '*';

    /**
     * Weekdays.
     * @var string
     */
    protected $weekdays = '*';

    /**
     * Symfony console application command e.g. "doctrine:schema:update".
     * @var string
     */
    protected $command;

    /**
     * Command description
     * @var string
     */
    protected $description;

    /**
     * If job is disalbed, it will not be synced to crontab.
     * @var bool
     */
    protected $disabled = false;

    /**
     * Output configuration. Usable keys are "file" and "append".
     * @var array
     */
    protected $output = [
        /**
         * The File which the output will be redirected into.
         * @var string
         */
        'file'   => null,

        /**
         * If true, the output will be appended to the configured output file.
         */
        'append' => null
    ];

    /**
     * Command arguments.
     * @var array
     */
    protected $arguments = [];

    /**
     * @param array|null $properties
     */
    public function __construct(array $properties = null)
    {
        if ($properties !== null) {
            $this->setProperties($properties);
        }
    }

    /**
     * Set the entity properties by given array.
     * @param array $properties
     * @return $this
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $setter = 'set' . ucfirst($key);
                $this->$setter($value);

            } else {
                throw new \InvalidArgumentException('Unknown property ' . $key . ' given.');
            }
        }

        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @codeCoverageIgnore
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @codeCoverageIgnore
     * @param string $minutes
     * @return $this
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @codeCoverageIgnore
     * @param string $hours
     * @return $this
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @codeCoverageIgnore
     * @param string $days
     * @return $this
     */
    public function setDays($days)
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @codeCoverageIgnore
     * @param string $months
     * @return $this
     */
    public function setMonths($months)
    {
        $this->months = $months;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getWeekdays()
    {
        return $this->weekdays;
    }

    /**
     * @codeCoverageIgnore
     * @param string $weekdays
     * @return $this
     */
    public function setWeekdays($weekdays)
    {
        $this->weekdays = $weekdays;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @codeCoverageIgnore
     * @param string $command
     * @return $this
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @codeCoverageIgnore
     * @param boolean $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool)$disabled;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @codeCoverageIgnore
     * @param array $output
     * @return $this
     */
    public function setOutput(array $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @codeCoverageIgnore
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments): Definition
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): Definition
    {
        $this->description = $description;
        return $this;
    }



}
