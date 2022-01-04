<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Crontab;

use Babymarkt\Symfony\CronBundle\Crontab\DefinitionChecker;
use Babymarkt\Symfony\CronBundle\Entity\Cron\Definition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

class DefinitionCheckerTest extends TestCase
{

    protected DefinitionChecker $checker;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $command = new Command('test:command');

        $this->checker = new DefinitionChecker();
        $this->checker->addCommand($command);
    }

    public function testValidDefinition()
    {
        $validDef = new Definition();
        $validDef->setCommand('test:command');

        $this->assertTrue($this->checker->check($validDef));
        $this->assertEmpty($this->checker->getResult());
    }

    public function testInvalidDefinition()
    {
        $validDef = new Definition();
        $validDef->setCommand('unknown:command');

        $this->assertFalse($this->checker->check($validDef));
        $this->assertEquals(DefinitionChecker::RESULT_COMMAND_NOT_FOUND, $this->checker->getResult());
    }
}
