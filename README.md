# CronBundle [![Build Status](https://travis-ci.org/Baby-Markt/cron-bundle.svg?branch=master)](https://travis-ci.org/Baby-Markt/cron-bundle)
A small bundle to manage cron entries in system crontab.

## Commands

Available commands are:

* babymarktext:cron:drop
* babymarktext:cron:dump
* babymarktext:cron:report
* babymarktext:cron:sync

### Drop
Drops all the whole crons block from crontab not considering the configured crons.

### Dump
Generates the cron entries which may be installed to crontab and shows it on console.

### Report
Show some reports about the execution of the configured crons. This features required the DoctrineBundle. 

### Sync
Syncs the configured crons with the crontab. Only the related cron block will be affected.

## Configuration

### defaults
Default configuration that affects the cron definitions.

#### output

### crontab
Configurations related to the crontab command.

#### bin
The path to the crontab binary. Defaults to "crontab".

#### tmpPath
The path for writing temporary files into. Defaults to system temp dir.

#### user
The user which will use to execute the command. Defaults to current user which executes the sync command.

#### sudo
If true, sudo will be used to execute the command. Defaults to "false".

### crons
The cron definitions.

#### minutes
Cron definition for minutes. Defaults to "*".

#### hours
Cron definition for hours. Defaults to "*".

#### days
Cron definition for days. Defaults to "*".

#### months
Cron definition for months. Defaults to "*".

#### weekdays
Cron definition for weekdays. Defaults to "*".

#### command
The symfony command which will be executed.

#### disabled
If true, the cron will not be synced to crontab. Defaults to "false".

#### arguments
A list of command arguments.

#### output
This configuration provides the same sub keys like defaults output. Here defined settings will overwrite the defaults.


