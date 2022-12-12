<?php

namespace Lumie\QuarterlySummary\Discount;

use Lumie\QuarterlySummary\Data\QuarterlySummary;

interface DiscountInterface
{
    public function getName(): string;
    public function getDiscountValue(): int;
    public function getDiscountType(): DiscountType;
    public function canApply(QuarterlySummary $summary): bool;
}
