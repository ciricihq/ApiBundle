<?php

namespace Cirici\ApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CiriciApiBundle extends Bundle
{
    public function getParent()
    {
        return "FOSUserBundle";
    }
}
