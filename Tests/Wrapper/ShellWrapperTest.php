<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Service\Wrapper {
    /**
     * Overwrite global exec method in class namespace.
     *
     * @param string $command
     * @param array $output
     * @param int $errorCode
     * @return string
     */
    function exec($command, &$output, &$errorCode)
    {
        $output    = ['FirstLine', 'LastLine'];
        $errorCode = 255;
        return 'command:' . $command;
    }
}

namespace BabymarktExt\CronBundle\Tests\Wrapper {


    use BabymarktExt\CronBundle\Service\Wrapper\ShellWrapper;

    class ShellWrapperTest extends \PHPUnit_Framework_TestCase
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
