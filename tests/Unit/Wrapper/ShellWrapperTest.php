<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Shell {
    function proc_open($command, array $descriptor_spec, &$pipes, ?string $cwd = null, ?array $env_vars = null, ?array $options = null)
    {
        $pipes = [0, 1, 2];
        return null;
    }

    function proc_close($pointer): int
    {
        return 255;
    }

    function stream_get_contents($stream, ?int $length = null, int $offset = -1): string
    {
        return [
            1 => 'FirstLine' . PHP_EOL . 'LastLine',
            2 => 'no crontab for user'
        ][$stream];
    }

    function fclose($pointer) {

    }
}

namespace Babymarkt\Symfony\CronBundle\Tests\Unit\Wrapper {

    use Babymarkt\Symfony\CronBundle\Shell\ShellWrapper;
    use PHPUnit\Framework\TestCase;

    class ShellWrapperTest extends TestCase
    {

        public function testCommandExecution()
        {
            $shell = new ShellWrapper();

            $result = $shell->execute('someCommand');

            $this->assertEquals('FirstLine', $result);
            $this->assertEquals(255, $shell->getErrorCode());
            $this->assertTrue($shell->isFailed());
            $this->assertEquals(['FirstLine', 'LastLine'], $shell->getOutput());
            $this->assertEquals('FirstLine' . PHP_EOL . 'LastLine', $shell->getOutputString());
            $this->assertEquals('no crontab for user', $shell->getErrorOutput());
        }


    }

}
