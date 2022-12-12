<?php

namespace Lumie\QuarterlySummary\Discount;

use Lumie\QuarterlySummary\Data\QuarterlySummary;

class SilverDiscount implements DiscountInterface
{
    public function getName(): string
    {
        return 'SILVER kedvezmény';
    }

    public function getDiscountType(): DiscountType
    {
        return DiscountType::Fixed;
    }

    public function getDiscountValue(): int
    {
        return 25000;
    }

    public function canApply(QuarterlySummary $summary): bool
    {
        foreach ($summary->getMonthlySummaries() as $monthlySummary) {
            if ($monthlySummary->getSent() >= 200000) {
                return true;
            }
        }

        return false;
    }
}
