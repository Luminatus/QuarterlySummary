<?php

namespace Lumie\QuarterlySummary\Data;

class YearlySummary
{
    protected $year;
    protected $company;

    /** @var QuarterlySummary[] $quarterlySummaries */
    protected $quarterlySummaries;

    public function __construct(Company $company, int $year)
    {
        $this->year = $year;
        $this->company = $company;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function addQuarterly(int $quarter, QuarterlySummary $summary)
    {
        $this->quarterlySummaries[$quarter] = $summary;
    }

    public function getQuarterlySummaries()
    {
        return $this->quarterlySummaries;
    }
}
