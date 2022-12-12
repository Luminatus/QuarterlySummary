<?php

namespace Lumie\QuarterlySummary\Controller;

use Lumie\QuarterlySummary\Kernel;

interface ControllerInterface
{
    public function setKernel(Kernel $kernel);
}
