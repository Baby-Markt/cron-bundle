<?php
/*
 * Copyright (c) 2015 Babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of Baby-Markt.de
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace BabymarktExt\CronBundle\Tests\Fixtures;

use BabymarktExt\CronBundle\DependencyInjection\BabymarktExtCronExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ContainerTrait
{

    /**
     * @var string
     */
    protected $projectDir = '/test/path';

    /**
     * @var string
     */
    protected $environment = 'test';

    /**
     * @param array $config
     * @return ContainerBuilder
     */
    protected function getContainer(array $config = []): ContainerBuilder
    {
        $ext  = new BabymarktExtCronExtension();
        $cont = new ContainerBuilder();
        $cont->setParameter('kernel.bundles', []);
        $cont->setParameter('kernel.project_dir', $this->projectDir);
        $cont->setParameter('kernel.environment', $this->environment);

        $ext->load([$config], $cont);

        return $cont;
    }

}
