<?php
declare(strict_types=1);

namespace Babymarkt\Symfony\CronBundle;

use Babymarkt\Symfony\CronBundle\DependencyInjection\Compiler\CommandDefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BabymarktCronBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CommandDefinitionPass());
    }


}
