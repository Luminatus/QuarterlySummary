<?php

namespace Lumie\QuarterlySummary\Data;

class MonthlySummary
{
    protected $month;
    protected $sent;

    public function __construct(int $month, int $sent)
    {
        $this->month = $month;
        $this->sent = $sent;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function getSent()
    {
        return $this->sent;
    }
}
