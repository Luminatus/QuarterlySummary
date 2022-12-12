<?php

namespace Lumie\QuarterlySummary\Controller;

use Lumie\QuarterlySummary\Kernel;

abstract class AbstractController implements ControllerInterface
{
    /** @var Kernel $kernel */
    protected $kernel;

    public function setKernel(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }
}
