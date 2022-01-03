# CronBundle [![Build Status](https://travis-ci.org/Baby-Markt/cron-bundle.svg?branch=master)](https://travis-ci.org/Baby-Markt/cron-bundle)
A small bundle to manage cron entries in system crontab.

## Commands

Available commands are:

* babymarkt_cron:drop
* babymarkt_cron:dump
* babymarkt_cron:report
* babymarkt_cron:sync

### Drop
Drops all the whole cronjobs block from crontab not considering the configured cronjobs.

### Dump
Generates the cron entries which may be installed to crontab and shows it on console.

### Report
Show some reports about the execution of the configured cronjobs. This features required the DoctrineBundle. 

### Sync
Syncs the configured cronjobs with the crontab. Only the related cron block will be affected.

## Configuration

Let's start with a minimal setup to run a job every minute:
```yaml
babymarkt_cron:
  cronjobs:
    my_job: 'my:symfony:command'
```

Full configuration reference with default values:
```yaml
babymarkt_cron:
    options:
        # This ID is used to identify the job block in the crontab. If not defined, 
        # it is automatically generated from the project directory and the environment.
        id: ~
        
        # The script to run the commands.
        script: 'bin/console'
        
        # The working directory. Defaults to %kernel.project_dir%.
        working_dir: ~
        
        # Specifies globally where the output should be written to.
        output:
            file: '/dev/null'
            append: true
        
        # Crontab options
        crontab:
            # Crontab executable.
            bin: 'crontab'
            # Path to store the temporary crontab content. Defaults to the system temp dir. 
            tmpPath: ~
            # The user to execute the crontab.
            user: ~
            # Defines whether sudo is used or not.
            sudo: false
    cronjobs:
        # The name of the job definition
        your-first-job-name:
            
            # Definition of the execution time.
            minutes: *
            hours: *
            days: *
            months: *
            weekdays: *
            
            # The Symfony command to execute.
            command: '<<the-symfony-command>>' # required
            
            # If TRUE, the command isn't executed.
            disabled: false

            # Overwrites the global output settings.
            output:
                file: ~
                append: ~
            
            # Command arguments and options.    
            arguments:
                - '<<your-first-argument>>'
                - '<<your-second-argument>>'
                #...
```
