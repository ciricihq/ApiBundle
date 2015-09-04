<?php

namespace Cirici\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Cirici\ApiBundle\CompilerPass\MappingCompilerPass;

class CiriciApiBundle extends Bundle
{
    public function getParent()
    {
        return "FOSUserBundle";
    }
}
