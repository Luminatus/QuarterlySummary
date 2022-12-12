<?php

namespace Lumie\QuarterlySummary\Data;

use Lumie\QuarterlySummary\Discount\DiscountInterface;
use Lumie\QuarterlySummary\Discount\DiscountType;

class QuarterlySummary
{

    /** @var MonthlySummary[] $monthlyData */
    protected $monthlyData;
    protected $previousSummary;

    protected $unitPrice;
    protected $quarter;

    /** @var DiscountInterface|null $appliedDiscount */
    protected $appliedDiscount;

    public function __construct(int $quarter, int $unitPrice, ?QuarterlySummary $previousSummary = null)
    {
        $this->unitPrice = $unitPrice;
        $this->previousSummary = $previousSummary;
        $this->quarter = $quarter;
        $this->monthlyData = [];
    }

    /** @return MonthlySummary[] */
    public function getMonthlySummaries(): array
    {
        return $this->monthlyData;
    }

    public function getPreviousSummary(): ?QuarterlySummary
    {
        return $this->previousSummary ?? null;
    }

    public function getQuarter(): int
    {
        return $this->quarter;
    }

    public function applyDiscount(DiscountInterface $discount)
    {
        $this->total = null;
        $this->appliedDiscount = $discount;
    }

    public function addMonthlySummary(MonthlySummary $monthlySummary)
    {
        $this->total = null;
        $this->monthlyData[$monthlySummary->getMonth()] = $monthlySummary;
    }

    public function getDiscount(): ?DiscountInterface
    {
        return $this->appliedDiscount;
    }

    public function calculateTotalSent()
    {
        $sent = 0;
        foreach ($this->monthlyData as $monthlySummary) {
            $sent += $monthlySummary->getSent();
        }

        return $sent;
    }

    public function calculateTotal(bool $applyDiscount = true): int
    {
        $total = 0;
        foreach ($this->monthlyData as $monthlySummary) {
            $total += $monthlySummary->getSent();
        }
        $total *= $this->unitPrice;

        if ($applyDiscount && $this->appliedDiscount) {
            switch ($this->appliedDiscount->getDiscountType()) {
                case DiscountType::Fixed:
                    $total -= $this->appliedDiscount->getDiscountValue();
                    break;
                case DiscountType::Percentage:
                    $total -= $total * ($this->appliedDiscount->getDiscountValue() / 100);
                    break;
            }
        }

        return $total;
    }
}
