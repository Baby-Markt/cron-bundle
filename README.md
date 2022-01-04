# CronBundle
A small bundle to manage cron entries in system crontab.

![Build 2.x](https://github.com/Baby-Markt/cron-bundle/actions/workflows/build.yml/badge.svg?branch=2.x)
[![codecov](https://codecov.io/gh/Baby-Markt/cron-bundle/branch/2.x/graph/badge.svg?token=98FGA3PEUD)](https://codecov.io/gh/Baby-Markt/cron-bundle)
[![Packagist Version](https://img.shields.io/packagist/v/babymarkt/cron-bundle)](https://packagist.org/packages/babymarkt/cron-bundle)
[![License](https://img.shields.io/github/license/Baby-Markt/cron-bundle.svg)](https://github.com/Baby-Markt/cron-bundle/blob/master/LICENSE)
![PHP from Packagist](https://img.shields.io/packagist/php-v/babymarkt/cron-bundle)

## Installation

You need to require this library through composer:

```bash
composer require babymarkt/cron-bundle
```

If you are using [Symfony Flex](https://github.com/symfony/flex), the following will happen automatically. Otherwise,
you have to enable the bundle on the `bundles.php` manually:

```php
// config/bundles.php
return [
    // ...
    Babymarkt\Symfony\CronBundle\BabymarktCronBundle::class => ['all' => true],
];

```

## Configuration

Let's start with a minimal setup to run a job every minute:
```yaml
babymarkt_cron:
  cronjobs:
    my_job: 'my:symfony:command'
```

After syncing the cronjobs with the command `bin/console babymarkt_cron:sync --env=prod`, 
following entry are created in the crontab:
```
###> /your/project-dir:prod ###
# job 'my_job' (no description)
* * * * * cd /your/project-dir; php bin/console --env=prod my:symfony:command 2>&1 1>>/dev/null
###< /your/project-dir:prod ###
```
To learn more about CRON see [CRON expression on Wikipedia](https://en.wikipedia.org/wiki/Cron#CRON_expression).

### Examples
A job running every day at 3:30 AM
```yaml
babymarkt_cron:
  cronjobs:
    my_job: 
      command: 'my:symfony:command'
      minutes: 30
      hours: 3
```

A job running every Tuesday every 10 minutes between 1:00 and 4:00 AM
```yaml
babymarkt_cron:
  cronjobs:
    my_job: 
      command: 'my:symfony:command'
      minutes: '*/10' # or '0,10,20,30,40,50'
      hours: '1-4'
      weekdays: 3 # Tuesday (SUN-SAT: 0-6)
```

### Full configuration reference
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
            
            # Definition of the execution time
            # See https://en.wikipedia.org/wiki/Cron#CRON_expression
            minutes: *
            hours: *
            days: *
            months: *
            weekdays: *
            
            # The Symfony command to execute.
            command: ~ # required
            
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

## Commands

### `babymarkt-cron:drop`
Drops all the whole cronjobs block from crontab not considering the configured cronjobs.

### `babymarkt-cron:dump`
Generates the cron entries which may be installed to crontab and shows it on console.

### `babymarkt-cron:report`
Show some reports about the execution of the configured cronjobs. This features required the DoctrineBundle.

### `babymarkt-cron:sync`
Syncs the configured cronjobs with the crontab. Only the related cron block will be affected.


## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/Baby-Markt/cron-bundle.

## License

The bundle is available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).
