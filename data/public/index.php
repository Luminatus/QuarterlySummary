<?php

use Lumie\QuarterlySummary\Kernel;

include "../vendor/autoload.php";

$kernel = new Kernel();

$kernel->init();

$kernel->run();
