<?php

namespace Lumie\QuarterlySummary\Discount;

use Lumie\QuarterlySummary\Data\QuarterlySummary;

class PartnerDiscount implements DiscountInterface
{
    public function getName(): string
    {
        return 'PARTNER kedvezmény';
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::Percentage;
    }

    public function getDiscountValue(): int
    {
        return 5;
    }

    public function canApply(QuarterlySummary $summary): bool
    {
        $sentSum = 0;

        foreach ($summary->getMonthlySummaries() as $monthlySummary) {
            $sentSum += $monthlySummary->getSent();
        }

        return $sentSum >= 400000;
    }
}
