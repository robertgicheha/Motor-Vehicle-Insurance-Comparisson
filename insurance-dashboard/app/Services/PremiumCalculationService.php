<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rate;

class PremiumCalculationService
{
    public function calculate(Company $company, $valuation, $includePVT = true, $hasAntiTheft = true, $isYoungDriver = false)
    {
        $premiumRate = Rate::where('company_id', $company->id)
            ->where('category', 'Premium Rate')
            ->where('item_name', 'Premium Rate')
            ->first();

        $pvtRate = Rate::where('company_id', $company->id)
            ->where('category', 'Premium Rate')
            ->where('item_name', 'PVT Rate')
            ->first();

        $pllRate = Rate::where('company_id', $company->id)
            ->where('category', 'Premium Rate')
            ->where('item_name', 'PLL Per Person')
            ->first();

        $excessOD = Rate::where('company_id', $company->id)
            ->where('category', 'Excess')
            ->where('item_name', 'Own Damage')
            ->first();

        $excessTheft = Rate::where('company_id', $company->id)
            ->where('category', 'Excess')
            ->where('item_name', 'Theft With Anti-Theft Device')
            ->first();

        $excessTheftNoDevice = Rate::where('company_id', $company->id)
            ->where('category', 'Excess')
            ->where('item_name', 'Theft Without Anti-Theft Device')
            ->first();

        $youngDriverExcess = Rate::where('company_id', $company->id)
            ->where('category', 'Excess')
            ->where('item_name', 'Novice & Young Drivers')
            ->first();

        $premium = 0;
        if ($premiumRate && $premiumRate->percentage) {
            $premium = $valuation * ($premiumRate->percentage / 100);
            if ($premiumRate->min_premium && $premium < $premiumRate->min_premium) {
                $premium = $premiumRate->min_premium;
            }
        }

        $pvt = 0;
        if ($includePVT && $pvtRate && $pvtRate->percentage) {
            $pvt = $valuation * ($pvtRate->percentage / 100);
            if ($pvtRate->min_value && $pvt < $pvtRate->min_value) {
                $pvt = $pvtRate->min_value;
            }
        }

        $excessODAmount = 0;
        if ($excessOD) {
            if ($excessOD->percentage) {
                $excessODAmount = $valuation * ($excessOD->percentage / 100);
                if ($excessOD->min_value && $excessODAmount < $excessOD->min_value) {
                    $excessODAmount = $excessOD->min_value;
                }
            }
        }

        $excessTheftAmount = 0;
        if ($hasAntiTheft && $excessTheft) {
            if ($excessTheft->percentage) {
                $excessTheftAmount = $valuation * ($excessTheft->percentage / 100);
                if ($excessTheft->min_value && $excessTheftAmount < $excessTheft->min_value) {
                    $excessTheftAmount = $excessTheft->min_value;
                }
            }
        } elseif (!$hasAntiTheft && $excessTheftNoDevice) {
            if ($excessTheftNoDevice->percentage) {
                $excessTheftAmount = $valuation * ($excessTheftNoDevice->percentage / 100);
                if ($excessTheftNoDevice->min_value && $excessTheftAmount < $excessTheftNoDevice->min_value) {
                    $excessTheftAmount = $excessTheftNoDevice->min_value;
                }
            }
        }

        $additionalExcess = 0;
        if ($isYoungDriver && $youngDriverExcess) {
            $additionalExcess = $youngDriverExcess->value ?? 0;
        }

        $total = $premium + $pvt;

        return [
            'company' => $company->name,
            'valuation' => $valuation,
            'premium_rate' => $premiumRate ? $premiumRate->percentage : 0,
            'premium' => round($premium, 2),
            'pvt_rate' => $pvtRate ? $pvtRate->percentage : 0,
            'pvt_premium' => round($pvt, 2),
            'total_premium' => round($total, 2),
            'excess_own_damage' => round($excessODAmount, 2),
            'excess_theft' => round($excessTheftAmount, 2),
            'excess_young_driver' => round($additionalExcess, 2),
        ];
    }
}
