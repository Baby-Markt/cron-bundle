<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Fixtures;


use Symfony\Component\Config\Definition\Exception\Exception;

trait StaticsLoaderTrait
{

    public function loadStaticFixture(string $file): string
    {

        $filePath = __DIR__ . '/statics/' . $file;

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }

        throw new Exception('Static fixture ' . $file . ' not found.');
    }

}
