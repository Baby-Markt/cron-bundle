<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\DependencyInjection;

use Babymarkt\Symfony\CronBundle\DependencyInjection\BabymarktCronExtension;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BabymarktCronExtensionTest extends TestCase
{
    private BabymarktCronExtension $extension;
    private string $root;
    private ContainerBuilder $container;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new BabymarktCronExtension();
        $this->container = new ContainerBuilder();
        $this->root      = "babymarkt_cron";

        $this->container->setParameter('kernel.bundles', []);
        $this->container->setParameter('kernel.project_dir', '/test/dir');
        $this->container->setParameter('kernel.environment', 'test');
        $this->container->setParameter('kernel.debug', 'false');
    }

    public function testFullConfigInjection()
    {
        $config = [
            'options'  => [
                'id'      => 'test-id',
                'output'  => [
                    'file'   => 'test',
                    'append' => true
                ],
                'crontab' => [
                    'bin'     => 'test',
                    'tmpPath' => 'test',
                    'user'    => 'test',
                    'sudo'    => true
                ]
            ],
            'cronjobs' => [
                'test_cron' => [
                    'minutes'     => '1',
                    'hours'       => '2',
                    'days'        => '3',
                    'months'      => '4',
                    'weekdays'    => '5',
                    'command'     => 'babymarkt-cron:validate',
                    'description' => null,
                    'disabled'    => true,
                    'output'      => ['file' => 'test', 'append' => true],
                    'arguments'   => ['test1', 'test2', 'test3']
                ]
            ]
        ];

        $this->extension->load([$config], $this->container);

        $this->assertEquals($config['options']['output'], $this->container->getParameter($this->root . '.options.output'));
        $this->assertEquals($config['options']['crontab'], $this->container->getParameter($this->root . '.options.crontab'));
        $this->assertEquals($config['options']['id'], $this->container->getParameter($this->root . '.options.id'));

        foreach ($this->container->getParameter($this->root . '.definitions') as $alias => $def) {
            $this->assertArrayHasKey($alias, $config['cronjobs']);
            $this->assertEquals($config['cronjobs'][$alias], $def);
        }
    }

    public function testConfigWithDefaultValues()
    {
        $this->extension->load([], $this->container);

        $this->assertTrue($this->container->hasParameter($this->root . '.options.crontab'));
        $this->assertTrue($this->container->hasParameter($this->root . '.options.id'));
        $this->assertTrue($this->container->hasParameter($this->root . '.options.output'));

        $this->assertEquals('/test/dir:test', $this->container->getParameter($this->root . '.options.id'));
    }

    public function testWithJobDefinitionAsString()
    {
        $configs = [
            'cronjobs' => [
                'test-cron' => 'my:test:command'
            ]
        ];

        $this->extension->load([$configs], $this->container);

        $definitions = $this->container->getParameter($this->root . '.definitions');

        $this->assertArrayHasKey('test_cron', $definitions);
        $this->assertArrayHasKey('command', $definitions['test_cron']);
        $this->assertEquals('my:test:command', $definitions['test_cron']['command']);

    }


    /**
     * @param array $definition
     * @throws \Exception
     * @dataProvider cronDefinitionData
     */
    public function testDefaultCronDefinition(array $definition)
    {
        $defaults = [
            'minutes'     => '*',
            'hours'       => '*',
            'days'        => '*',
            'months'      => '*',
            'weekdays'    => '*',
            'command'     => 'babymarkt-cron:validate',
            'description' => null,
            'disabled'    => false,
            'output'      => ['file' => null, 'append' => null],
            'arguments'   => []
        ];

        $configs = [
            'cronjobs' => [
                'test-cron' => array_replace_recursive(['command' => 'babymarkt-cron:validate'], $definition)
            ]
        ];

        $this->extension->load([$configs], $this->container);

        $this->assertEquals(
            ['test_cron' => array_replace_recursive($defaults, $definition)],
            $this->container->getParameter($this->root . '.definitions')
        );
    }



    /**
     * @return Generator
     */
    public function cronDefinitionData(): Generator
    {
        yield [['minutes' => 1]];
        yield [['hours' => 1]];
        yield [['days' => 1]];
        yield [['months' => 1]];
        yield [['weekdays' => 1]];
    }

}
