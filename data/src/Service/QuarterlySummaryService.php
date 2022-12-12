<?php

namespace Lumie\QuarterlySummary\Service;

use Lumie\QuarterlySummary\Data\Company;
use Lumie\QuarterlySummary\Data\MonthlySummary;
use Lumie\QuarterlySummary\Data\QuarterlySummary;
use Lumie\QuarterlySummary\Data\YearlySummary;
use Lumie\QuarterlySummary\Discount\DiscountInterface;
use Lumie\QuarterlySummary\Discount\DiscountType;
use PDO;

class QuarterlySummaryService
{
    const UNIT_PRICE = 10;

    /** @var PDO $db */
    protected $db;

    /** @var DiscountInterface[] $discounts */
    protected $discounts;

    public function __construct(PDO $db, array $discounts)
    {
        $this->db = $db;
        $this->discounts = $discounts;
    }

    /**
     * @return QuarterlySummary[]
     */
    public function getYearlySummary(int $companyId, int $year): YearlySummary
    {
        dump($companyId, $year);
        $statement = $this->db->prepare('
            SELECT c.id, c.name, s.year, s.month, s.sent
            FROM company c
            LEFT JOIN sending_stat s ON s.company_id = c.id
            WHERE 
                c.id = :id AND
                s.year = :year
            ORDER BY s.month ASC
        ');
        $statement->bindParam(':id', $companyId, PDO::PARAM_INT);
        $statement->bindParam(':year', $year, PDO::PARAM_INT);

        $statement->execute();

        $rawData = $statement->fetchAll(PDO::FETCH_ASSOC);

        /** @var QuarterlySummary[] $quarterlySummaries */
        $quarterlySummaries = [];

        if (!count($rawData)) {
            return $quarterlySummaries;
        }

        $company = new Company($companyId, $rawData[0]['name']);

        $yearlySummary = new YearlySummary($company, $year);

        foreach ($rawData as $monthlyData) {
            $monthlySummary = new MonthlySummary($monthlyData['month'], $monthlyData['sent']);
            $quarter = intdiv($monthlySummary->getMonth() - 1, 3) + 1;
            if (!array_key_exists($quarter, $quarterlySummaries)) {
                $quarterlySummaries[$quarter] = new QuarterlySummary($quarter, static::UNIT_PRICE, $quarterlySummaries[$quarter - 1] ?? null);
            }
            $quarterlySummaries[$quarter]->addMonthlySummary($monthlySummary);
        }

        foreach ($quarterlySummaries as $summary) {
            if ($summary->calculateTotal() > 0) {
                foreach ($this->discounts as $discount) {
                    if ($discount->canApply($summary) && $this->discountHigherThanCurrent($summary, $discount)) {
                        $summary->applyDiscount($discount);
                    }
                }

                $yearlySummary->addQuarterly($summary->getQuarter(), $summary);
            }
        }

        return $yearlySummary;
    }

    private function discountHigherThanCurrent(QuarterlySummary $summary, DiscountInterface $discount)
    {
        if (!$summary->getDiscount()) {
            return true;
        }

        if ($summary->getDiscount()->getDiscountType() == $discount->getDiscountType()) {
            return $summary->getDiscount()->getDiscountValue() > $discount->getDiscountValue();
        } else {
            $total = $summary->calculateTotal(false);

            if ($discount->getDiscountType() == DiscountType::Percentage) {
                return $total * $discount->getDiscountValue() / 100 > $summary->getDiscount()->getDiscountValue();
            } else {
                return $discount->getDiscountValue() > $total * $summary->getDiscount()->getDiscountValue() / 100;
            }
        }
    }
}
