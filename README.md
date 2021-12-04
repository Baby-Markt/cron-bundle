# CronBundle [![Build Status](https://travis-ci.org/Baby-Markt/cron-bundle.svg?branch=master)](https://travis-ci.org/Baby-Markt/cron-bundle)
A small bundle to manage cron entries in system crontab.

## Commands

Available commands are:

* babymarktext:cron:drop
* babymarktext:cron:dump
* babymarktext:cron:report
* babymarktext:cron:sync

### Drop
Drops all the whole cronjobs block from crontab not considering the configured cronjobs.

### Dump
Generates the cron entries which may be installed to crontab and shows it on console.

### Report
Show some reports about the execution of the configured cronjobs. This features required the DoctrineBundle. 

### Sync
Syncs the configured cronjobs with the crontab. Only the related cron block will be affected.

## Configuration

Following all available configuration options:
```
    babymarkt_ext_cron:
        options:
            id: '<<your-custom-id>>'
            script: 'bin/console'
            working_dir: '%kernel.project_dir%'
            output:
                file: '/dev/null'
                append: false
            crontab:
                bin: 'crontab'
                tmpPath: '<<system temp dir>>'
                user: null
                sudo: false
        cronjobs:
            your-first-job-name:
                minutes: *
                hours: *
                days: *
                months: *
                weekdays: *
                command: '<<the-symfony-command>>'
                disabled: false
                output:
                    file: null
                    append: null
                arguments:
                    - '<<your-first-argument>>'
                    - '<<your-second-argument>>'
                    - '...'
            your-second-job-name:
                ...
```

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

### cronjobs
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


