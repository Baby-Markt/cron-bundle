<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle\Tests\Fixtures;

use Babymarkt\Symfony\CronBundle\DependencyInjection\BabymarktCronExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ContainerTrait
{

    /**
     * @var string
     */
    protected string $projectDir = '/test/path';

    /**
     * @var string
     */
    protected string $environment = 'test';

    /**
     * @param array $config
     * @return ContainerBuilder
     * @throws \Exception
     * @throws \Exception
     */
    protected function getContainer(array $config = []): ContainerBuilder
    {
        $ext  = new BabymarktCronExtension();
        $cont = new ContainerBuilder();
        $cont->setParameter('kernel.bundles', []);
        $cont->setParameter('kernel.project_dir', $this->projectDir);
        $cont->setParameter('kernel.environment', $this->environment);

        $ext->load([$config], $cont);

        return $cont;
    }

}
