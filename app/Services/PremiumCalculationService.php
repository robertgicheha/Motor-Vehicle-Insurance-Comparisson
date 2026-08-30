<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rate;

class PremiumCalculationService
{
    public function calculate(
        Company $company,
        $valuation,
        $vehicleType = 'psv',
        $includePVT = true,
        $numPassengers = 1,
        $hasAntiTheft = true,
        $isYoungDriver = false
    ) {
        $rates = Rate::where('company_id', $company->id)
            ->where('vehicle_type', $vehicleType)
            ->get()
            ->keyBy(function ($r) {
                return $r->category . '::' . $r->item_name;
            });

        $premiumRate = $rates->get('Premium Rate::Premium Rate');
        $pvtRate = $rates->get('Premium Rate::PVT Rate');
        $pllRate = $rates->get('Premium Rate::PLL Per Person');
        $trainingLevyRate = $rates->get('Premium Rate::Training Levy Rate');
        $policyHolderFundRate = $rates->get('Premium Rate::Policy Holder Fund Rate');
        $stampDuty = $rates->get('Premium Rate::Stamp Duty');
        $premiumRateAbove = $rates->get('Premium Rate::Premium Rate Above 2.5M');

        $excessOD = $rates->get('Excess::Own Damage');
        $excessTheft = $rates->get('Excess::Theft With Anti-Theft Device');
        $excessTheftNoDevice = $rates->get('Excess::Theft Without Anti-Theft Device');
        $youngDriverExcess = $rates->get('Excess::Novice & Young Drivers');

        $isPvtInclusive = $premiumRate && $premiumRate->is_pvt_inclusive;

        $ratePercent = $premiumRate ? (float) $premiumRate->percentage : 0;
        $minPremium = $premiumRate ? (float) $premiumRate->min_premium : 0;

        if ($premiumRateAbove && $valuation > 2500000) {
            $ratePercent = (float) $premiumRateAbove->percentage;
            if ($premiumRateAbove->min_premium) {
                $minPremium = (float) $premiumRateAbove->min_premium;
            }
        }

        $basicPremium = $valuation * ($ratePercent / 100);
        if ($basicPremium < $minPremium) {
            $basicPremium = $minPremium;
        }

        $pvt = 0;
        if ($includePVT) {
            if ($isPvtInclusive) {
                $pvt = 0;
            } elseif ($pvtRate && $pvtRate->percentage) {
                $pvt = $valuation * ($pvtRate->percentage / 100);
                if ($pvtRate->min_value && $pvt < $pvtRate->min_value) {
                    $pvt = (float) $pvtRate->min_value;
                }
            }
        }

        $pll = 0;
        if ($pllRate && $pllRate->value) {
            $pll = (float) $pllRate->value * $numPassengers;
        }

        // The combined levy (previously a single "Levis Rate") is split into its
        // two regulatory components so they can be shown/edited separately.
        $trainingLevy = 0;
        if ($trainingLevyRate && $trainingLevyRate->percentage) {
            $trainingLevy = ($basicPremium + $pvt + $pll) * ($trainingLevyRate->percentage / 100);
        }
        $policyHolderFund = 0;
        if ($policyHolderFundRate && $policyHolderFundRate->percentage) {
            $policyHolderFund = ($basicPremium + $pvt + $pll) * ($policyHolderFundRate->percentage / 100);
        }
        $levis = $trainingLevy + $policyHolderFund;

        $stampDutyAmount = 0;
        if ($stampDuty && $stampDuty->value) {
            $stampDutyAmount = (float) $stampDuty->value;
        }

        $excessODAmount = 0;
        if ($excessOD) {
            if ($excessOD->percentage) {
                $excessODAmount = $valuation * ($excessOD->percentage / 100);
                if ($excessOD->min_value && $excessODAmount < $excessOD->min_value) {
                    $excessODAmount = (float) $excessOD->min_value;
                }
                if ($excessOD->max_value && $excessODAmount > $excessOD->max_value) {
                    $excessODAmount = (float) $excessOD->max_value;
                }
            }
        }

        $excessTheftAmount = 0;
        if ($hasAntiTheft && $excessTheft) {
            if ($excessTheft->percentage) {
                $excessTheftAmount = $valuation * ($excessTheft->percentage / 100);
                if ($excessTheft->min_value && $excessTheftAmount < $excessTheft->min_value) {
                    $excessTheftAmount = (float) $excessTheft->min_value;
                }
            }
        } elseif (!$hasAntiTheft && $excessTheftNoDevice) {
            if ($excessTheftNoDevice->percentage) {
                $excessTheftAmount = $valuation * ($excessTheftNoDevice->percentage / 100);
                if ($excessTheftNoDevice->min_value && $excessTheftAmount < $excessTheftNoDevice->min_value) {
                    $excessTheftAmount = (float) $excessTheftNoDevice->min_value;
                }
            }
        }

        $additionalExcess = 0;
        if ($isYoungDriver && $youngDriverExcess) {
            $additionalExcess = (float) ($youngDriverExcess->value ?? 0);
        }

        $totalPremium = $basicPremium + $pll + $pvt + $levis + $stampDutyAmount;

        return [
            'company' => $company->name,
            'company_slug' => $company->slug,
            'valuation' => $valuation,
            'vehicle_type' => $vehicleType,
            'premium_rate' => $ratePercent,
            'basic_premium' => round($basicPremium, 2),
            'is_pvt_inclusive' => $isPvtInclusive,
            'pvt_rate' => $pvtRate ? (float) $pvtRate->percentage : 0,
            'pvt_premium' => round($pvt, 2),
            'pll_per_person' => $pllRate ? (float) $pllRate->value : 0,
            'pll_total' => round($pll, 2),
            'training_levy_rate' => $trainingLevyRate ? (float) $trainingLevyRate->percentage : 0,
            'training_levy' => round($trainingLevy, 2),
            'policy_holder_fund_rate' => $policyHolderFundRate ? (float) $policyHolderFundRate->percentage : 0,
            'policy_holder_fund' => round($policyHolderFund, 2),
            'levis_rate' => ($trainingLevyRate ? (float) $trainingLevyRate->percentage : 0) + ($policyHolderFundRate ? (float) $policyHolderFundRate->percentage : 0),
            'levis' => round($levis, 2),
            'stamp_duty' => $stampDutyAmount,
            'total_premium' => round($totalPremium, 2),
            'excess_own_damage' => round($excessODAmount, 2),
            'excess_theft' => round($excessTheftAmount, 2),
            'excess_young_driver' => round($additionalExcess, 2),
        ];
    }
}
