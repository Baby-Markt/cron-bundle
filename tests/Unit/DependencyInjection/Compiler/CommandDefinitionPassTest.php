<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Unit\DependencyInjection\Compiler;

use Babymarkt\Symfony\CronBundle\Crontab\DefinitionChecker;
use Babymarkt\Symfony\CronBundle\DependencyInjection\Compiler\CommandDefinitionPass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CommandDefinitionPassTest extends TestCase
{

    protected Container $container;

    /**
     * @var MockObject|Definition
     */
    protected MockObject $definition;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();

        $definitionStub = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addMethodCall'])
            ->getMock();

        $this->container->setDefinition(DefinitionChecker::class, $definitionStub);

        $this->definition = $definitionStub;
    }

    public function testProcessWithOneTaggedServiceFound()
    {
        $definition = (new Definition(\stdClass::class))
            ->setPublic(true)
            ->addTag('console.command');
        $serviceId  = 'some_command';
        $this->container->setDefinition($serviceId, $definition);

        $this->definition->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->equalTo('addCommand'),
                $this->callback(static function ($v) {
                    return $v[0] instanceof Reference;
                })
            );

        $compilerPass = new CommandDefinitionPass();
        $compilerPass->process($this->container);
    }

    public function testProcessWithNoTaggedServicesFound()
    {
        $this->definition->expects($this->never())->method('addMethodCall');

        $compilerPass = new CommandDefinitionPass();
        $compilerPass->process($this->container);
    }

    public function testProcessWithNoRegistryServiceAvailable()
    {
        $containerStub = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findDefinition', 'has'])
            ->getMock();

        $containerStub->expects($this->once())->method('has')->willReturn(false);
        $containerStub->expects($this->never())->method('findDefinition');

        $compilerPass = new CommandDefinitionPass();
        $compilerPass->process($containerStub);

    }
}
