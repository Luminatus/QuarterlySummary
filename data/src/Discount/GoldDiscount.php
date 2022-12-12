<?php

namespace Lumie\QuarterlySummary\Discount;

use Lumie\QuarterlySummary\Data\QuarterlySummary;

class GoldDiscount implements DiscountInterface
{
    public function getName(): string
    {
        return 'GOLD kedvezmény';
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::Percentage;
    }

    public function getDiscountValue(): int
    {
        return 10;
    }

    public function canApply(QuarterlySummary $summary): bool
    {
        $prevSummary = $summary->getPreviousSummary();
        if (!$prevSummary) {
            return false;
        }

        $sentSum = 0;

        foreach ($prevSummary->getMonthlySummaries() as $monthlySummary) {
            $sentSum += $monthlySummary->getSent();
        }

        return $sentSum >= 800000;
    }
}
