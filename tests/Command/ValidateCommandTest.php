<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Command;


use Babymarkt\Symfony\CronBundle\Command\ValidateJobsCommand;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use Babymarkt\Symfony\CronBundle\Service\DefinitionChecker;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ValidateCommandTest extends TestCase
{
    use ContainerTrait;

    public function testValidDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->once())
            ->method('check')
            ->with($this->isInstanceOf(Definition::class))
            ->willReturn(true);

        $checkerStub->expects($this->never())->method('getResult');

        $definitions = [
            'test' => ['command' => 'some:command']
        ];

        $cmd = new ValidateJobsCommand($checkerStub, $definitions);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString('OK', $tester->getDisplay());
    }

    public function testInvalidDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->once())
            ->method('check')
            ->with($this->isInstanceOf(Definition::class))
            ->willReturn(false);

        $checkerStub->expects($this->once())
            ->method('getResult')
            ->willReturn(DefinitionChecker::RESULT_INCORRECT_COMMAND);

        $definitions = [
            'test' => ['command' => 'some:command']
        ];

        $cmd = new ValidateJobsCommand($checkerStub, $definitions);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:validate'));
        $tester->execute([]);

        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString(DefinitionChecker::RESULT_INCORRECT_COMMAND, $tester->getDisplay());
    }

    public function testDisabledDefinition()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->never())
            ->method('check');

        $checkerStub->expects($this->never())
            ->method('getResult');

        $definitions = [
            'test' => ['command' => 'some:command', 'disabled' => true]
        ];

        $cmd = new ValidateJobsCommand($checkerStub, $definitions);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('test', $tester->getDisplay());
        $this->assertStringContainsString('some:command', $tester->getDisplay());
        $this->assertStringContainsString('Disabled', $tester->getDisplay());
    }

    public function testNoDefinitionsFound()
    {
        $checkerStub = $this->getMockBuilder(DefinitionChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $checkerStub->expects($this->never())->method('check');
        $checkerStub->expects($this->never())->method('getResult');

        $cmd = new ValidateJobsCommand($checkerStub, []);

        $app = new Application();
        $app->add($cmd);

        $tester = new CommandTester($app->find('babymarkt-cron:validate'));
        $tester->execute([]);

        $this->assertEmpty($tester->getStatusCode());
        $this->assertStringContainsString('No cron job definitions found', $tester->getDisplay());

    }
}
