<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Unit\Crontab\Factory;

use Babymarkt\Symfony\CronBundle\Crontab\CrontabEntryGenerator;
use Babymarkt\Symfony\CronBundle\Crontab\Factory\CrontabEntryGeneratorFactory;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Babymarkt\Symfony\CronBundle\Tests\Unit\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;

class CrontabEntryGeneratorFactoryTest extends TestCase
{
    use ContainerTrait;


    public function testFactory()
    {
        $config = [
            'cronjobs' => [
                'test-job' => [
                    'command'  => 'babymarktext:test:command'
                ]
            ]
        ];

        $container = $this->getContainer($config);

        $factory = new CrontabEntryGeneratorFactory(
            $container->getParameter('babymarkt_cron.definitions'),
            $container->getParameter('babymarkt_cron.options.output'),
            $container->getParameter('kernel.project_dir') . '/..',
            $container->getParameter('kernel.environment')
        );

        $generator = $factory->create();

        $this->assertInstanceOf(CrontabEntryGenerator::class, $generator);
        $this->assertContainsOnlyInstancesOf(Definition::class, $generator->getDefinitions());
    }
}
