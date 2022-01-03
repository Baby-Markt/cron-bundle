<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Symfony\CronBundle\Tests\Fixtures;


use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class StaticsLoaderTrait
 * @package Babymarkt\Symfony\CronBundle\Tests\Fixtures
 */
trait StaticsLoaderTrait
{

    /**
     * @param $file
     * @return string
     */
    public function loadStaticFixture($file)
    {

        $filePath = __DIR__ . '/statics/' . $file;

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }

        throw new Exception('Static fixture ' . $file . ' not found.');
    }

}
