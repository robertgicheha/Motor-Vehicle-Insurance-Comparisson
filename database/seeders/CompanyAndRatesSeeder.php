<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Rate;
use Illuminate\Database\Seeder;

class CompanyAndRatesSeeder extends Seeder
{
    public function run(): void
    {
        $sanlam = Company::create([
            'name' => 'Sanlam Allianz',
            'slug' => 'sanlam-allianz',
            'description' => 'Sanlam Allianz Insurance - Comprehensive Motor Vehicle Insurance',
            'is_active' => true,
        ]);

        $ga = Company::create([
            'name' => 'GA Insurance',
            'slug' => 'ga-insurance',
            'description' => 'GA Insurance - Comprehensive Motor Vehicle Insurance',
            'is_active' => true,
        ]);

        $coverItems = [
            ['Windscreen / Glass', 50000, 75000],
            ['Car Entertainment', 50000, 75000],
            ['Towing & Recovery Expenses', 50000, 75000],
            ['Authorized Repair', 50000, 100000],
            ['Medical Expenses', 50000, 50000],
        ];

        foreach ($coverItems as $i => $item) {
            Rate::create([
                'company_id' => $sanlam->id,
                'category' => 'Cover Limits',
                'item_name' => $item[0],
                'value' => $item[1],
                'value_type' => 'fixed',
            ]);
            Rate::create([
                'company_id' => $ga->id,
                'category' => 'Cover Limits',
                'item_name' => $item[0],
                'value' => $item[2],
                'value_type' => 'fixed',
            ]);
        }

        $thirdPartyItems = [
            ['Third Party Bodily Injury - Per Person', 3000000, 3000000],
            ['Third Party Bodily Injury - Per Event', 20000000, 10000000],
            ['Passenger Liability Limit - Any One Person', 3000000, 3000000],
            ['Passenger Liability Limit - Any One Event', 20000000, 20000000],
            ['Third Party Property Damage Limit', 5000000, 10000000],
        ];

        foreach ($thirdPartyItems as $item) {
            Rate::create([
                'company_id' => $sanlam->id,
                'category' => 'Third Party Limits',
                'item_name' => $item[0],
                'value' => $item[1],
                'value_type' => 'fixed',
            ]);
            Rate::create([
                'company_id' => $ga->id,
                'category' => 'Third Party Limits',
                'item_name' => $item[0],
                'value' => $item[2],
                'value_type' => 'fixed',
            ]);
        }

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Own Damage',
            'percentage' => 10,
            'min_value' => 50000,
            'value_type' => 'percentage_min',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Own Damage',
            'percentage' => 7.5,
            'min_value' => 30000,
            'value_type' => 'percentage_min',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Partial Theft',
            'percentage' => 10,
            'min_value' => 50000,
            'value_type' => 'percentage_min',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Partial Theft',
            'percentage' => 7.5,
            'min_value' => 30000,
            'value_type' => 'percentage_min',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Theft With Anti-Theft Device',
            'percentage' => 10,
            'min_value' => 50000,
            'value_type' => 'percentage_min',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Theft With Anti-Theft Device',
            'percentage' => 10,
            'min_value' => 30000,
            'value_type' => 'percentage_min',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Theft Without Anti-Theft Device',
            'percentage' => 30,
            'min_value' => 50000,
            'value_type' => 'percentage_min',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Theft Without Anti-Theft Device',
            'percentage' => 30,
            'min_value' => 30000,
            'value_type' => 'percentage_min',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Third Party Bodily Injury',
            'value' => 50000,
            'value_type' => 'fixed',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Third Party Bodily Injury',
            'value' => 30000,
            'value_type' => 'fixed',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Third Party Property Damage',
            'value' => 50000,
            'value_type' => 'fixed',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Third Party Property Damage',
            'value' => 10000,
            'value_type' => 'fixed',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Excess',
            'item_name' => 'Novice & Young Drivers',
            'value' => 50000,
            'value_type' => 'fixed',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Excess',
            'item_name' => 'Novice & Young Drivers',
            'value' => 7500,
            'value_type' => 'fixed',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Premium Rate',
            'item_name' => 'Premium Rate',
            'percentage' => 6.5,
            'min_premium' => 75000,
            'value_type' => 'percentage',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Premium Rate',
            'item_name' => 'Premium Rate',
            'percentage' => 7.5,
            'min_premium' => 35000,
            'value_type' => 'percentage',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Premium Rate',
            'item_name' => 'PLL Per Person',
            'value' => 500,
            'value_type' => 'fixed',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Premium Rate',
            'item_name' => 'PLL Per Person',
            'value' => 500,
            'value_type' => 'fixed',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Premium Rate',
            'item_name' => 'PVT Rate',
            'percentage' => 0.5,
            'min_value' => 5000,
            'value_type' => 'percentage',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Premium Rate',
            'item_name' => 'PVT Rate',
            'percentage' => 0.5,
            'min_value' => 5000,
            'value_type' => 'percentage',
        ]);

        Rate::create([
            'company_id' => $sanlam->id,
            'category' => 'Coverage Features',
            'item_name' => 'Geographical Area',
            'value' => 'Kenya',
            'value_type' => 'text',
        ]);
        Rate::create([
            'company_id' => $ga->id,
            'category' => 'Coverage Features',
            'item_name' => 'Geographical Area',
            'value' => 'East Africa',
            'value_type' => 'text',
        ]);

        $features = [
            ['Special Perils', 'Covered', 'Covered'],
            ['Motor Trade', 'Covered', 'Covered'],
            ['No Blame, No Excess', 'Covered - subject to police abstract', 'No Cover'],
            ['Riot, Strike and Civil Commotion', 'Covered', 'Covered'],
            ['Fatal Personal Accident', 'No Cover', 'Covered - 250,000/-'],
            ['Sanlam Allianz Emergency Assist', 'Included', 'N/A'],
        ];

        foreach ($features as $feature) {
            Rate::create([
                'company_id' => $sanlam->id,
                'category' => 'Coverage Features',
                'item_name' => $feature[0],
                'value' => 0,
                'value_type' => 'text',
            ]);
            Rate::create([
                'company_id' => $ga->id,
                'category' => 'Coverage Features',
                'item_name' => $feature[0],
                'value' => 0,
                'value_type' => 'text',
            ]);
        }
    }
}
