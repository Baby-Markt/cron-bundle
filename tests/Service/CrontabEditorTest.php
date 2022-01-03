<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Service;

use Babymarkt\Symfony\CronBundle\Service\CrontabEditor;
use Babymarkt\Symfony\CronBundle\Service\Reader\CrontabReaderInterface;
use Babymarkt\Symfony\CronBundle\Tests\Fixtures\BufferedWriter;
use PHPUnit\Framework\TestCase;

class CrontabEditorTest extends TestCase
{

    public function testRemovingCronDefinitions()
    {
        $reader = $this->getMockBuilder(CrontabReaderInterface::class)->getMock();
        $writer = new BufferedWriter();

        $reader->method('read')->willReturn([
            'row 1',
            'row 2',
            '###> test ###',
            'some cron definition 1',
            'some cron definition 2',
            '###< test ###',
            'row 99',
            'row 100',
        ]);

        $editor = new CrontabEditor('test', $reader, $writer);

        $editor->removeCronjobs();

        $buffer = $writer->getClean();

        $this->assertCount(4, $buffer);
        $this->assertNotContains('###> test ###', $buffer);
        $this->assertNotContains('###< test ###', $buffer);
        $this->assertNotContains('some cron definition 1', $buffer);
        $this->assertNotContains('some cron definition 2', $buffer);
    }

    public function testRemovingCronjobsWithMultipleBlocks()
    {
        $reader = $this->getMockBuilder(CrontabReaderInterface::class)->getMock();
        $writer = new BufferedWriter();

        $reader->method('read')->willReturn([
            'row 1',
            '###> test ###',
            'some cron definition test',
            '###< test ###',
            'row 2',
            '###> some_other ###',
            'some cron definition other',
            '###< some_other ###',
            'row 3',
        ]);

        $editor = new CrontabEditor('test', $reader, $writer);

        $editor->removeCronjobs();

        $buffer = $writer->getClean();

        $this->assertCount(6, $buffer);
        $this->assertNotContains('###> test ###', $buffer);
        $this->assertNotContains('###< test ###', $buffer);
        $this->assertContains('###> some_other ###', $buffer);
        $this->assertContains('###< some_other ###', $buffer);
        $this->assertNotContains('some cron definition test', $buffer);
        $this->assertContains('some cron definition other', $buffer);
    }

    public function testUpdateExistingCronjobsBlock()
    {
        $reader = $this->getMockBuilder(CrontabReaderInterface::class)->getMock();
        $writer = new BufferedWriter();

        $reader->method('read')->willReturn([
            'row 1',
            'row 2',
            '###> test ###',
            'some old cron definition 1',
            'some old cron definition 2',
            '###< test ###',
            'row 99',
            'row 100',
        ]);

        $editor = new CrontabEditor('test', $reader, $writer);

        $editor->injectCronjobs([
            'cron-1' => 'the new cron definition 1',
            'cron-2' => 'the new cron definition 2'
        ]);

        $buffer = $writer->getClean();

        $this->assertCount(8, $buffer);
        $this->assertContains('###> test ###', $buffer);
        $this->assertContains('###< test ###', $buffer);
        $this->assertNotContains('some old cron definition 1', $buffer);
        $this->assertNotContains('some old cron definition 2', $buffer);
        $this->assertContains('the new cron definition 1', $buffer);
        $this->assertContains('the new cron definition 2', $buffer);
    }

    public function testInjectingCronjobs()
    {
        $reader = $this->getMockBuilder(CrontabReaderInterface::class)->getMock();
        $writer = new BufferedWriter();

        $reader->method('read')->willReturn([
            'row 1',
            'row 2',
        ]);

        $editor = new CrontabEditor('test', $reader, $writer);

        $editor->injectCronjobs([
            'cron-1' => 'cron definition 1',
            'cron-2' => 'cron definition 2'
        ]);

        $buffer = $writer->getClean();

        $this->assertContains('###> test ###', $buffer);
        $this->assertContains('###< test ###', $buffer);
        $this->assertContains('cron definition 1', $buffer);
        $this->assertContains('cron definition 2', $buffer);
    }

    /**
     *
     */
    public function testStrposEmptyNeedle()
    {
        $reader = $this->getMockBuilder(CrontabReaderInterface::class)->getMock();
        $writer = new BufferedWriter();

        $reader->method('read')->willReturn([
            '',
            'row 1',
            'row 2',
            '###> test ###',
            '',
            'some old cron definition 1',
            'some old cron definition 2',
            '###< test ###',
            'row 99',
            '',
            'row 100',
        ]);

        $editor = new CrontabEditor('test', $reader, $writer);

        $editor->removeCronjobs();

        $buffer = $writer->getClean();

        $this->assertCount(6, $buffer);
        $this->assertNotContains('###> test ###', $buffer);
        $this->assertNotContains('###< test ###', $buffer);
        $this->assertNotContains('some cron definition 1', $buffer);
        $this->assertNotContains('some cron definition 2', $buffer);
        $this->assertContains('', $buffer);
    }
}
