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
     */
    public function setProperties(array $properties): Definition
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
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setAlias(string $alias): Definition
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMinutes(): string
    {
        return $this->minutes;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMinutes(string $minutes): Definition
    {
        $this->minutes = $minutes;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getHours(): string
    {
        return $this->hours;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setHours(string $hours): Definition
    {
        $this->hours = $hours;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDays(): string
    {
        return $this->days;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDays(string $days): Definition
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getMonths(): string
    {
        return $this->months;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setMonths(string $months): Definition
    {
        $this->months = $months;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getWeekdays(): string
    {
        return $this->weekdays;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setWeekdays(string $weekdays): Definition
    {
        $this->weekdays = $weekdays;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setCommand(string $command): Definition
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDisabled(bool $disabled): Definition
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getOutput(): array
    {
        return $this->output;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setOutput(array $output): Definition
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setArguments(array $arguments): Definition
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setDescription(?string $description): Definition
    {
        $this->description = $description;
        return $this;
    }

}
