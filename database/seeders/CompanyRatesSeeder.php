<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Rate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanyRatesSeeder extends Seeder
{
    public function run(): void
    {
        $sanlam = Company::firstOrCreate([
            'slug' => 'sanlam-allianz',
        ], [
            'name' => 'Sanlam Allianz Insurance',
            'description' => 'Sanlam Allianz Insurance Company Limited',
            'is_active' => true,
        ]);

        $ga = Company::firstOrCreate([
            'slug' => 'ga-insurance-ltd',
        ], [
            'name' => 'GA Insurance Ltd',
            'description' => 'GA Insurance Ltd',
            'is_active' => true,
        ]);

        $this->seedSanlamRates($sanlam);
        $this->seedGARates($ga);
    }

    private function seedSanlamRates(Company $c): void
    {
        $rates = [
            // ── Premium Rate ──
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 6.5, 'value_type' => 'percentage', 'min_premium' => 75000, 'notes' => '6.5% of vehicle value, min premium Kes. 75,000 inclusive of PVT'],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.5, 'value_type' => 'percentage', 'min_value' => 5000,   'notes' => '0.5% for PVT, min Kes. 5,000/-'],
            ['category' => 'Premium Rate', 'item_name' => 'PLL Per Person',      'value' => 500,      'value_type' => 'fixed',                               'notes' => 'Kes. 500/- per person'],
            ['category' => 'Premium Rate', 'item_name' => 'IRA Levy',            'percentage' => 0.5, 'value_type' => 'percentage',                          'notes' => '0.5% of basic premium'],

            // ── Cover Limits ──
            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Glass',             'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Car Entertainment',               'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing & Recovery Expenses',      'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Authorized Repair',               'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Medical Expenses',                'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],

            // ── Third Party Limits ──
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kes. 3,000,000/- per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kes. 20,000,000/- per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability Per Person',       'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kes. 3,000,000/- any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability Per Event',        'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kes. 20,000,000/- any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',    'value' => 5000000,  'value_type' => 'fixed', 'notes' => 'Kes. 5,000,000/-'],

            // ── Excess ──
            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft With Anti-Theft Device',       'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft Without Anti-Theft Device',    'percentage' => 30, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '30% min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Novice & Young Drivers',             'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/- additional'],

            // ── Additional Cover ──
            ['category' => 'Additional Cover', 'item_name' => 'Including Special Perils',                      'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor Trade',                                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'No Blame No Excess',                           'value' => 0, 'value_type' => 'text', 'notes' => 'Covered - subject to police abstract blaming third party vehicle'],
            ['category' => 'Additional Cover', 'item_name' => 'Riot Strike and Civil Commotion',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Fatal Personal Accident',                      'value' => 0, 'value_type' => 'text', 'notes' => 'No Cover'],
            ['category' => 'Additional Cover', 'item_name' => 'Sanlam Allianz Emergency Assist',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Geographical Area',                            'value' => 0, 'value_type' => 'text', 'notes' => 'Kenya'],
        ];

        foreach ($rates as $rate) {
            $c->rates()->updateOrCreate(
                ['category' => $rate['category'], 'item_name' => $rate['item_name']],
                $rate
            );
        }
    }

    private function seedGARates(Company $c): void
    {
        $rates = [
            // ── Premium Rate ──
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 7.5, 'value_type' => 'percentage', 'min_premium' => 35000, 'notes' => '7.5% of vehicle value, min premium Kes. 35,000 basic'],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.5, 'value_type' => 'percentage', 'min_value' => 5000,   'notes' => '0.5% for PVT, min Kes. 5,000/-'],
            ['category' => 'Premium Rate', 'item_name' => 'PLL Per Person',      'value' => 500,      'value_type' => 'fixed',                               'notes' => 'Kes. 500/- per person'],
            ['category' => 'Premium Rate', 'item_name' => 'IRA Levy',            'percentage' => 0.5, 'value_type' => 'percentage',                          'notes' => '0.5% of basic premium'],

            // ── Cover Limits ──
            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Glass',             'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Car Entertainment',               'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing & Recovery Expenses',      'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Authorized Repair',               'value' => 100000, 'value_type' => 'fixed', 'notes' => 'Kes. 100,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Medical Expenses',                'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],

            // ── Third Party Limits ──
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kes. 3,000,000/- per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury Per Event', 'value' => 10000000, 'value_type' => 'fixed', 'notes' => 'Kes. 10,000,000/- per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability Per Person',       'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kes. 3,000,000/- any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability Per Event',        'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kes. 20,000,000/- any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',    'value' => 10000000, 'value_type' => 'fixed', 'notes' => 'Kes. 10,000,000/-'],

            // ── Excess ──
            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 7.5, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '7.5% min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 7.5, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '7.5% min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft With Anti-Theft Device',       'percentage' => 10,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '10% min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft Without Anti-Theft Device',    'percentage' => 30,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '30% min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 0,                               'value_type' => 'fixed',       'notes' => 'Nil'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 10000,                             'value_type' => 'fixed',       'notes' => 'Kes. 10,000/-'],
            ['category' => 'Excess', 'item_name' => 'Novice & Young Drivers',             'value' => 7500,                              'value_type' => 'fixed',       'notes' => 'Kes. 7,500/- additional'],

            // ── Additional Cover ──
            ['category' => 'Additional Cover', 'item_name' => 'Including Special Perils',                      'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor Trade',                                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'No Blame No Excess',                           'value' => 0, 'value_type' => 'text', 'notes' => 'No Cover'],
            ['category' => 'Additional Cover', 'item_name' => 'Riot Strike and Civil Commotion',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Fatal Personal Accident',                      'value' => 250000, 'value_type' => 'fixed', 'notes' => 'Covered - Kes. 250,000/-'],
            ['category' => 'Additional Cover', 'item_name' => 'Geographical Area',                            'value' => 0, 'value_type' => 'text', 'notes' => 'East Africa'],
        ];

        foreach ($rates as $rate) {
            $c->rates()->updateOrCreate(
                ['category' => $rate['category'], 'item_name' => $rate['item_name']],
                $rate
            );
        }
    }
}
