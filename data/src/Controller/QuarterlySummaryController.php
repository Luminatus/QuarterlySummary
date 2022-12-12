<?php

namespace Lumie\QuarterlySummary\Controller;

use Lumie\QuarterlySummary\Data\QuarterlySummary;
use Lumie\QuarterlySummary\Data\YearlySummary;
use Lumie\QuarterlySummary\Request\Request;
use Lumie\QuarterlySummary\Service\QuarterlySummaryService;

class QuarterlySummaryController extends AbstractController
{
    public function get(Request $request)
    {
        /** @var QuarterlySummaryService $service */
        $service = $this->kernel->getService('quarterlySummary');

        /** @var YearlySummary $summary */
        $summary = $service->getYearlySummary(intval($request->param('company_id')), intval($request->param('year')));

        $viewData = [
            'company' => $summary->getCompany()->getName(),
            'year' => $summary->getYear(),
            'quarterlySummaries' => array_map([$this, 'formatQuarterlySummary'], $summary->getQuarterlySummaries())
        ];
    }

    protected function formatQuarterlySummary(QuarterlySummary $summary)
    {
        $total = $summary->calculateTotal(false);
        $discountedTotal = $summary->getDiscount() ? $summary->calculateTotal() : $total;

        return [
            'quarter' => $summary->getQuarter(),
            'sent' => $summary->calculateTotalSent(),
            'total' => $total,
            'discountName' => $summary->getDiscount()->getName() ?? '',
            'discountAmount' => $total - $discountedTotal,
            'discountedTotal' => $discountedTotal
        ];
    }
}
