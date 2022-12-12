<?php

namespace Lumie\QuarterlySummary\Controller;

use Lumie\QuarterlySummary\Request\Request;

class QuarterlySummaryController implements ControllerInterface
{
    public function get(Request $request)
    {
        dump("here we are", $request);
    }
}
