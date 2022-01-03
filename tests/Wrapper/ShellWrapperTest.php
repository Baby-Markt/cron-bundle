<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Service\Wrapper {
    /**
     * Overwrite global exec method in class namespace.
     *
     * @param string $command
     * @param array $output
     * @param int $errorCode
     * @return string
     */
    function exec(string $command, array &$output, int &$errorCode): string
    {
        $output    = ['FirstLine', 'LastLine'];
        $errorCode = 255;
        return 'command:' . $command;
    }
}

namespace Babymarkt\Symfony\CronBundle\Tests\Wrapper {

    use Babymarkt\Symfony\CronBundle\Service\Wrapper\ShellWrapper;
    use PHPUnit\Framework\TestCase;

    class ShellWrapperTest extends TestCase
    {

        public function testCommandExecution()
        {
            $shell = new ShellWrapper();

            $result = $shell->execute('someCommand');

            $this->assertEquals('command:someCommand', $result);
            $this->assertEquals(255, $shell->getErrorCode());
            $this->assertTrue($shell->isFailed());
            $this->assertEquals(['FirstLine', 'LastLine'], $shell->getOutput());
            $this->assertEquals('FirstLine' . PHP_EOL . 'LastLine', $shell->getOutputString());
        }


    }

}
