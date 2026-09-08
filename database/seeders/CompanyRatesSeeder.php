<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Rate;
use Illuminate\Database\Seeder;

class CompanyRatesSeeder extends Seeder
{
    public function run(): void
    {
        // Cleanup: remove renamed old Medical Expenses items for private vehicle type
        \App\Models\Rate::where('vehicle_type', 'private')
            ->where('item_name', 'Medical Expenses')
            ->where('category', 'Cover Limits')
            ->delete();

        // ─────────────────────────────────────────────────────────────
        // 1. SANLAM ALLIANZ (PSV + Private)
        // ─────────────────────────────────────────────────────────────
        $sanlam = Company::updateOrCreate(['slug' => 'sanlam-allianz'], [
            'name' => 'Sanlam Allianz',
            'description' => 'Sanlam Allianz Insurance Company Limited',
            'is_active' => true,
            'company_type' => 'both',
            'min_sum_insured_psv' => 850000,
            'min_sum_insured_private' => 500000,
        ]);

        // ── PSV Rates ──
        $sanlamPsv = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 6.5, 'value_type' => 'percentage', 'min_premium' => 75000, 'notes' => '6.5% Minimum Premium- Kes. 75,000 Inclusive of PVT', 'is_pvt_inclusive' => true],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.5, 'value_type' => 'percentage', 'min_value' => 5000,   'notes' => '0.5% for PVT, Min 5,000/-'],
            ['category' => 'Premium Rate', 'item_name' => 'PLL Per Person',      'value' => 500,      'value_type' => 'fixed',                               'notes' => 'PLL per person - 500/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',          'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business', 'value' => 10,      'value_type' => 'fixed',                               'notes' => 'Maximum Age New business- 10 years'],
            ['category' => 'Premium Rate', 'item_name' => 'Minimum Sum Insured',  'value' => 0,       'value_type' => 'text',                                'notes' => 'Minimum Sum Insured- Kes. 850,000'],
            ['category' => 'Premium Rate', 'item_name' => 'Excess Protector',     'value' => 0,       'value_type' => 'text',                                'notes' => 'No cover for Excess Protector (Own Damage/Theft)'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Glass',             'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Car Entertainment',               'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing & Recovery Expenses',      'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Authorized Repair',               'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Medical Expenses',                'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Geographical Area',               'value' => 0,      'value_type' => 'text', 'notes' => 'Kenya'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 5000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 5,000,000'],

            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% Min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% Min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft With Anti-Theft Device',       'percentage' => 10, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '10% Min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft Without Anti-Theft Device',    'percentage' => 30, 'min_value' => 50000, 'value_type' => 'percentage_min', 'notes' => '30% Min Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/-'],
            ['category' => 'Excess', 'item_name' => 'Novice & Young Drivers',             'value' => 50000,                             'value_type' => 'fixed',       'notes' => 'Kes. 50,000/- additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Including special perils',                      'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor trade',                                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'No blame no excess',                           'value' => 0, 'value_type' => 'text', 'notes' => 'Covered - subject to police abstract blaming third party vehicle'],
            ['category' => 'Additional Cover', 'item_name' => 'Riot, strike and civil commotion',              'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Fatal Personal Accident',                      'value' => 0, 'value_type' => 'text', 'notes' => 'No Cover'],
            ['category' => 'Additional Cover', 'item_name' => 'Sanlam Allianz Emergency Assist',                'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
        ];
        $this->seedRates($sanlam, $sanlamPsv, 'psv');

        // ── Private Rates ──
        $sanlamPrivate = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',              'percentage' => 4,   'value_type' => 'percentage', 'min_premium' => 37500, 'notes' => "500,000/- to 2,500,000/- : 4% Min 37,500 inclusive of Excess P.", 'is_pvt_inclusive' => false],
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate Above 2.5M',   'percentage' => 3,   'value_type' => 'percentage', 'min_premium' => 37500, 'notes' => "Above 2,500,000/- : 3% , Inclusive of Excess P.", 'is_pvt_inclusive' => false],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',                  'percentage' => 0.25,'value_type' => 'percentage', 'min_value' => 3000,   'notes' => '0.25% Min 3,000/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',                'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business',       'value' => 10,      'value_type' => 'fixed',                               'notes' => 'Max 10 years vehicle age'],
            ['category' => 'Premium Rate', 'item_name' => 'Declaration of Use',        'value' => 0,       'value_type' => 'text',                                'notes' => 'Declaration of use on Toyota Probox, Success, Noah, Voxy and Sienta'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Window Glass',  'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Entertainment unit',         'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing and recovery after an accident', 'value' => 75000, 'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Repair Authority',           'value' => 100000, 'value_type' => 'fixed', 'notes' => 'Kshs. 100,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Emergency medical expenses - Occupants', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Emergency medical expenses - Third Party', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Vehicle Valuation',          'value' => 0,      'value_type' => 'text',  'notes' => 'Free of charge at Renewal / Inception'],

            ['category' => 'Extras', 'item_name' => 'ATM Robbery following Hijacking',                  'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Overnight Accommodation expenses after accident',  'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Loss of personal effects in an accident',          'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Side Mirrors',                                    'value' => 0,      'value_type' => 'text',  'notes' => 'Kshs. 100,000 with an Excess 10% of loss Min Kshs. 5,000/- - Limit Subject to valuation report'],
            ['category' => 'Extras', 'item_name' => 'Spare Wheel, Jack and other Accessories',          'value' => 0,      'value_type' => 'text',  'notes' => 'Kshs. 50,000 with an Excess 10% of loss Min Kshs. 5,000/- Cover Limit subject to valuation report'],
            ['category' => 'Extras', 'item_name' => 'Geographical Area',                               'value' => 0,      'value_type' => 'text',  'notes' => 'East Africa (subject to COMESA cover for third party liabilities)'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 30000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 30,000,000'],

            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 30000,                             'value_type' => 'fixed',       'notes' => 'Kshs. 30,000'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 30000,                             'value_type' => 'fixed',       'notes' => 'Kshs. 30,000'],
            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 2.5, 'min_value' => 30000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value minimum Kshs.30,000 maximum 100,000. No blame no Excess - subject to police abstract blaming third party'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 2.5, 'min_value' => 30000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value Min Ksh 30,000 Max, Ksh 100,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - With anti theft devices', 'percentage' => 10, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '10% of vehicle value minimum Kshs.30,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - Without anti theft devices', 'percentage' => 20, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '20% of vehicle value minimum Kshs.30,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft with satellite tracking device', 'percentage' => 0, 'value_type' => 'text', 'notes' => 'Nil subject to proof of vehicle fitted with valid & functional tracking Device'],
            ['category' => 'Excess', 'item_name' => 'Novice Driver under 1 Year',         'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],
            ['category' => 'Excess', 'item_name' => 'Young Drivers under 21 Years',       'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Special Perils A-H',                           'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Non-Political Riot & Strike',                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Earthquake, Tsunami & Other Weather conditions', 'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Whilst in custody of motor trader',            'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Jurisdiction Clause',                          'value' => 0, 'value_type' => 'text', 'notes' => 'Kenya'],
            ['category' => 'Additional Cover', 'item_name' => 'Legal Liability for passengers',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Liability for passengers acts of negligence',  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor contingent Liability',                   'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Malicious Damage',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Cancellation Notice',                          'value' => 0, 'value_type' => 'text', 'notes' => '30 days'],
            ['category' => 'Additional Cover', 'item_name' => 'PVT',                                         'value' => 0, 'value_type' => 'text', 'notes' => '0.25%, Minimum Kes.3,000 per unit (Reinstatement once per year)'],
            ['category' => 'Additional Cover', 'item_name' => 'Excess Protector',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered (Reinstatement at 0.25% Min Kshs. 5,000/-, Reinstatement once per year)'],
            ['category' => 'Additional Cover', 'item_name' => 'Reinstatement for windscreen/window glass & entertainment cover', 'value' => 0, 'value_type' => 'text', 'notes' => 'Charge 10% of required limit Min reinstatement premiums 3,000/-'],

            ['category' => 'Optional Benefits', 'item_name' => 'Loss of use',                               'value' => 0,      'value_type' => 'text', 'notes' => 'Kes. 3,000 - 10 days / Kes. 6,000 - 20 days / Kes. 9,000 - 30 days'],
            ['category' => 'Optional Benefits', 'item_name' => 'AA Road Rescue',                             'value' => 0,      'value_type' => 'text', 'notes' => 'SAZ-Assist is inclusive under the cover. Any other services may be procured directly from the preferred provider.'],
        ];
        $this->seedRates($sanlam, $sanlamPrivate, 'private');

        // ─────────────────────────────────────────────────────────────
        // 2. GA INSURANCE LTD (PSV + Private)
        // ─────────────────────────────────────────────────────────────
        $ga = Company::updateOrCreate(['slug' => 'ga-insurance-ltd'], [
            'name' => 'GA Insurance Ltd',
            'description' => 'GA Insurance Ltd',
            'is_active' => true,
            'company_type' => 'both',
            'min_sum_insured_psv' => 800000,
            'min_sum_insured_private' => 500000,
        ]);

        // ── PSV Rates ──
        $gaPsv = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 7.5, 'value_type' => 'percentage', 'min_premium' => 35000, 'notes' => '7.5% Min Premium- Kes. 35,000, basic', 'is_pvt_inclusive' => false],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.5, 'value_type' => 'percentage', 'min_value' => 5000,   'notes' => '0.5% for PVT Min 5,000/-'],
            ['category' => 'Premium Rate', 'item_name' => 'PLL Per Person',      'value' => 500,      'value_type' => 'fixed',                               'notes' => 'PLL per person - 500/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',          'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business', 'value' => 12,      'value_type' => 'fixed',                               'notes' => 'Maximum Age New business- 12 years'],
            ['category' => 'Premium Rate', 'item_name' => 'Minimum Sum Insured',  'value' => 0,       'value_type' => 'text',                                'notes' => 'Minimum Sum Insured- Kes. 800,000'],
            ['category' => 'Premium Rate', 'item_name' => 'Excess Protector',     'value' => 0,       'value_type' => 'text',                                'notes' => 'No cover for Excess Protector (Own Damage/Theft)'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Glass',             'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Car Entertainment',               'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing & Recovery Expenses',      'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kes. 75,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Authorized Repair',               'value' => 100000, 'value_type' => 'fixed', 'notes' => 'Kes. 100,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Medical Expenses',                'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Geographical Area',               'value' => 0,      'value_type' => 'text', 'notes' => 'East Africa'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 10000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 10,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 10000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 10,000,000'],

            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 7.5, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '7.5% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 7.5, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '7.5% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft With Anti-Theft Device',       'percentage' => 10,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '10% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft Without Anti-Theft Device',    'percentage' => 30,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '30% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 0,                               'value_type' => 'fixed',       'notes' => 'Nil'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 10000,                             'value_type' => 'fixed',       'notes' => 'Kes. 10,000/-'],
            ['category' => 'Excess', 'item_name' => 'Novice & Young Drivers',             'value' => 7500,                              'value_type' => 'fixed',       'notes' => 'Kes. 7,500/- additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Including special perils',                      'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor trade',                                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'No blame no excess',                           'value' => 0, 'value_type' => 'text', 'notes' => 'No Cover'],
            ['category' => 'Additional Cover', 'item_name' => 'Riot, strike and civil commotion',              'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Fatal Personal Accident',                      'value' => 250000, 'value_type' => 'fixed', 'notes' => 'Covered - 250,000/-'],
        ];
        $this->seedRates($ga, $gaPsv, 'psv');

        // ── Private Rates ──
        $gaPrivate = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 4,   'value_type' => 'percentage', 'min_premium' => 30000, 'notes' => "4% , Min Prem 30,000/- Basic", 'is_pvt_inclusive' => false],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.25,'value_type' => 'percentage', 'min_value' => 2500,   'notes' => '0.25%, Min Kes.2,500/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',          'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business', 'value' => 15,      'value_type' => 'fixed',                               'notes' => 'Age Limit : 15yrs'],
            ['category' => 'Premium Rate', 'item_name' => 'Minimum Sum Insured',  'value' => 500000,  'value_type' => 'fixed',                               'notes' => 'Min Sums Insured: 500,000/-'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Window Glass',  'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Entertainment unit',         'value' => 75000,  'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing and recovery after an accident', 'value' => 75000, 'value_type' => 'fixed', 'notes' => 'Kshs. 75,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Repair Authority',           'value' => 100000, 'value_type' => 'fixed', 'notes' => 'Kshs. 100,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Emergency medical expenses - Occupants', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Vehicle Valuation',          'value' => 0,      'value_type' => 'text',  'notes' => 'Free of charge at Renewal / Inception'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 5000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 5,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 5000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 5,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 10000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 10,000,000'],

            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 0,                               'value_type' => 'fixed',       'notes' => 'Nil'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 7500,                              'value_type' => 'fixed',       'notes' => 'Kshs. 7,500'],
            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 2.5, 'min_value' => 20000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value minimum Kshs.20,000 maximum 100,000. No blame no Excess - subject to police abstract blaming third party'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 2.5, 'min_value' => 20000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value Min Ksh 20,000 Max, Ksh 100,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - With anti theft devices', 'percentage' => 10, 'min_value' => 20000, 'value_type' => 'percentage_min', 'notes' => '10% of vehicle value minimum Kshs.20,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - Without anti theft devices', 'percentage' => 20, 'min_value' => 20000, 'value_type' => 'percentage_min', 'notes' => '20% of vehicle value minimum Kshs.20,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft with satellite tracking device', 'percentage' => 2.5, 'min_value' => 20000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value minimum Kshs.20,000'],
            ['category' => 'Excess', 'item_name' => 'Novice Driver under 1 Year',         'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],
            ['category' => 'Excess', 'item_name' => 'Young Drivers under 21 Years',       'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Special Perils A-H',                           'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Non-Political Riot & Strike',                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Earthquake, Tsunami & Other Weather conditions', 'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Whilst in custody of motor trader',            'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Jurisdiction Clause',                          'value' => 0, 'value_type' => 'text', 'notes' => 'Kenya'],
            ['category' => 'Additional Cover', 'item_name' => 'Legal Liability for passengers',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Liability for passengers acts of negligence',  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor contingent Liability',                   'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Malicious Damage',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Cancellation Notice',                          'value' => 0, 'value_type' => 'text', 'notes' => '30 days'],
            ['category' => 'Additional Cover', 'item_name' => 'PVT',                                         'value' => 0, 'value_type' => 'text', 'notes' => 'Covered, 0.25%, Min Kes.2,500/-'],
            ['category' => 'Additional Cover', 'item_name' => 'Excess Protector',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered, 0.25% Min Kshs. 2,500/-'],
            ['category' => 'Additional Cover', 'item_name' => 'Reinstatement for windscreen/window glass & entertainment cover', 'value' => 0, 'value_type' => 'text', 'notes' => 'Charge 10% of required limit Min reinstatement premiums 3,000/-'],

            ['category' => 'Optional Benefits', 'item_name' => 'Loss of use',                               'value' => 0,      'value_type' => 'text', 'notes' => 'Kes. 3,000 - 10 days'],
            ['category' => 'Optional Benefits', 'item_name' => 'AA Road Rescue',                             'value' => 5500,   'value_type' => 'fixed', 'notes' => 'Kes. 5,500/-'],
        ];
        $this->seedRates($ga, $gaPrivate, 'private');

        // ─────────────────────────────────────────────────────────────
        // 3. DEFINITE Assurance (PSV only)
        // ─────────────────────────────────────────────────────────────
        $definite = Company::updateOrCreate(['slug' => 'definite-insurance'], [
            'name' => 'Definite Assurance',
            'description' => 'Definite Assurance Limited',
            'is_active' => true,
            'company_type' => 'psv',
            'min_sum_insured_psv' => 500000,
            'min_sum_insured_private' => null,
        ]);

        $definitePsv = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 5.5, 'value_type' => 'percentage', 'min_premium' => 37500, 'notes' => '5.5% Min Premium- Kes. 37,500/-', 'is_pvt_inclusive' => false],
            ['category' => 'Premium Rate', 'item_name' => 'PVT Rate',            'percentage' => 0.45,'value_type' => 'percentage', 'min_value' => 3500,   'notes' => '0.45% for PVT Min 3,500/-'],
            ['category' => 'Premium Rate', 'item_name' => 'PLL Per Person',      'value' => 500,      'value_type' => 'fixed',                               'notes' => 'PLL per person - 500/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',          'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business', 'value' => 15,      'value_type' => 'fixed',                               'notes' => 'Maximum Age New business- 15 years'],
            ['category' => 'Premium Rate', 'item_name' => 'Minimum Sum Insured',  'value' => 0,       'value_type' => 'text',                                'notes' => 'Minimum Sum Insured- Kes. 500,000'],
            ['category' => 'Premium Rate', 'item_name' => 'Excess Protector',     'value' => 0,       'value_type' => 'text',                                'notes' => 'No cover for Excess Protector (Own Damage/Theft)'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Glass',             'value' => 30000,  'value_type' => 'fixed', 'notes' => 'Kes. 30,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Car Entertainment',               'value' => 30000,  'value_type' => 'fixed', 'notes' => 'Kes. 30,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing & Recovery Expenses',      'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Authorized Repair',               'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kes. 50,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Medical Expenses',                'value' => 30000,  'value_type' => 'fixed', 'notes' => 'Kes. 30,000/-'],
            ['category' => 'Cover Limits', 'item_name' => 'Geographical Area',               'value' => 0,      'value_type' => 'text', 'notes' => 'East Africa'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 5000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 5,000,000'],

            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 5,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '5% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 5,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '5% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft With Anti-Theft Device',       'percentage' => 10, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '10% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft Without Anti-Theft Device',    'percentage' => 25, 'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '25% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Theft with a tracking device',       'percentage' => 5,  'min_value' => 30000, 'value_type' => 'percentage_min', 'notes' => '5% Min Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 30000,                            'value_type' => 'fixed',       'notes' => 'Kes. 30,000/-'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 15000,                            'value_type' => 'fixed',       'notes' => 'Kes. 15,000/-'],
            ['category' => 'Excess', 'item_name' => 'Novice & Young Drivers',             'value' => 10000,                            'value_type' => 'fixed',       'notes' => 'Kes. 10,000/- additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Including special perils',                      'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Riot, strike and civil commotion',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Fatal Personal Accident',                      'value' => 0, 'value_type' => 'text', 'notes' => 'No Cover'],
        ];
        $this->seedRates($definite, $definitePsv, 'psv');

        // ─────────────────────────────────────────────────────────────
        // 4. BRITAM (Private only)
        // ─────────────────────────────────────────────────────────────
        $britam = Company::updateOrCreate(['slug' => 'britam'], [
            'name' => 'BRITAM',
            'description' => 'BRITAM Insurance Company Limited',
            'is_active' => true,
            'company_type' => 'private',
            'min_sum_insured_psv' => null,
            'min_sum_insured_private' => 500000,
        ]);

        $britamPrivate = [
            ['category' => 'Premium Rate', 'item_name' => 'Premium Rate',        'percentage' => 3.5, 'value_type' => 'percentage', 'min_premium' => 45000, 'notes' => "3.5% Incl. of Excess P. and PVT, Min premium per unit 45,000/-", 'is_pvt_inclusive' => true],
            ['category' => 'Premium Rate', 'item_name' => 'Training Levy Rate',    'percentage' => 0.2, 'value_type' => 'percentage',                          'notes' => 'Training Levy — 0.2% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Policy Holder Fund Rate','percentage' => 0.25,'value_type' => 'percentage',                          'notes' => 'Policy Holder Fund — 0.25% of the premium'],
            ['category' => 'Premium Rate', 'item_name' => 'Stamp Duty',          'value' => 40,       'value_type' => 'fixed',                               'notes' => 'Stamp Duty Kes. 40/-'],
            ['category' => 'Premium Rate', 'item_name' => 'Max Age New Business', 'value' => 8,       'value_type' => 'fixed',                               'notes' => 'Max 8 years vehicle age (2018 onward)'],

            ['category' => 'Cover Limits', 'item_name' => 'Windscreen / Window Glass',  'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Entertainment unit',         'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Towing and recovery after an accident', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Repair Authority',           'value' => 50000,  'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Emergency medical expenses - Occupants', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Emergency medical expenses - Third Party', 'value' => 50000, 'value_type' => 'fixed', 'notes' => 'Kshs. 50,000'],
            ['category' => 'Cover Limits', 'item_name' => 'Vehicle Valuation',          'value' => 0,      'value_type' => 'text',  'notes' => 'Free of charge at Renewal / Inception'],

            ['category' => 'Extras', 'item_name' => 'ATM Robbery following Hijacking',                  'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Overnight Accommodation expenses after accident',  'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Loss of personal effects in an accident',          'value' => 20000,  'value_type' => 'fixed', 'notes' => 'Kshs. 20,000'],
            ['category' => 'Extras', 'item_name' => 'Side Mirrors',                                    'value' => 0,      'value_type' => 'text',  'notes' => 'Kshs. 50,000 with an Excess 5% of loss Min Kshs. 5,000/-'],
            ['category' => 'Extras', 'item_name' => 'Spare Wheel, Jack and other Accessories',          'value' => 0,      'value_type' => 'text',  'notes' => 'Kshs. 30,000 with an Excess 5% of loss Min Kshs. 5,000/-'],
            ['category' => 'Extras', 'item_name' => 'Geographical Area',                               'value' => 0,      'value_type' => 'text',  'notes' => 'East Africa (subject to COMESA cover for third party liabilities)'],

            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Person', 'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 per person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Bodily Injury - Per Event', 'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 per event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one person',     'value' => 3000000,  'value_type' => 'fixed', 'notes' => 'Kshs. 3,000,000 any one person'],
            ['category' => 'Third Party Limits', 'item_name' => 'Passenger Liability any one event',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000 any one event'],
            ['category' => 'Third Party Limits', 'item_name' => 'Third Party Property Damage Limit',      'value' => 20000000, 'value_type' => 'fixed', 'notes' => 'Kshs. 20,000,000'],

            ['category' => 'Excess', 'item_name' => 'Third Party Bodily Injury',          'value' => 0,                               'value_type' => 'fixed',       'notes' => 'Nil'],
            ['category' => 'Excess', 'item_name' => 'Third Party Property Damage',        'value' => 7500,                              'value_type' => 'fixed',       'notes' => 'Kshs. 7,500'],
            ['category' => 'Excess', 'item_name' => 'Own Damage',                         'percentage' => 2.5, 'min_value' => 30000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value minimum Kshs.30,000 maximum 100,000. No blame no Excess - subject to police abstract blaming third party'],
            ['category' => 'Excess', 'item_name' => 'Partial Theft',                      'percentage' => 2.5, 'min_value' => 30000, 'max_value' => 100000, 'value_type' => 'percentage_min', 'notes' => '2.5% of value Min Ksh 30,000 Max, Ksh 100,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - With anti theft devices', 'percentage' => 10, 'min_value' => 20000, 'value_type' => 'percentage_min', 'notes' => '10% of vehicle value minimum Kshs.20,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft - Without anti theft devices', 'percentage' => 20, 'min_value' => 20000, 'value_type' => 'percentage_min', 'notes' => '20% of vehicle value minimum Kshs.20,000'],
            ['category' => 'Excess', 'item_name' => 'Total Theft with satellite tracking device', 'percentage' => 0, 'value_type' => 'text', 'notes' => 'Nil subject to proof of vehicle fitted with valid & functional tracking Device'],
            ['category' => 'Excess', 'item_name' => 'Novice Driver under 1 Year',         'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],
            ['category' => 'Excess', 'item_name' => 'Young Drivers under 21 Years',       'value' => 5000,                              'value_type' => 'fixed',       'notes' => 'Kshs. 5,000 Additional'],

            ['category' => 'Additional Cover', 'item_name' => 'Special Perils A-H',                           'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Non-Political Riot & Strike',                  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Earthquake, Tsunami & Other Weather conditions', 'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Whilst in custody of motor trader',            'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Jurisdiction Clause',                          'value' => 0, 'value_type' => 'text', 'notes' => 'Kenya'],
            ['category' => 'Additional Cover', 'item_name' => 'Legal Liability for passengers',               'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Liability for passengers acts of negligence',  'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Motor contingent Liability',                   'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Malicious Damage',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Cancellation Notice',                          'value' => 0, 'value_type' => 'text', 'notes' => '30 days'],
            ['category' => 'Additional Cover', 'item_name' => 'PVT',                                         'value' => 0, 'value_type' => 'text', 'notes' => 'Covered'],
            ['category' => 'Additional Cover', 'item_name' => 'Excess Protector',                             'value' => 0, 'value_type' => 'text', 'notes' => 'Covered (Reinstatement at 0.25% Min Kshs. 3,000/-)'],
            ['category' => 'Additional Cover', 'item_name' => 'Reinstatement for windscreen/window glass & entertainment cover', 'value' => 0, 'value_type' => 'text', 'notes' => 'Charge 10% of required limit Min reinstatement premiums 3,000/-'],

            ['category' => 'Optional Benefits', 'item_name' => 'Loss of use',                               'value' => 0,      'value_type' => 'text', 'notes' => 'Kes. 3,000 - 10 days'],
            ['category' => 'Optional Benefits', 'item_name' => 'AA Road Rescue',                             'value' => 6500,   'value_type' => 'fixed', 'notes' => 'Kes. 6,500/-'],
        ];
        $this->seedRates($britam, $britamPrivate, 'private');
    }

    private function seedRates(Company $company, array $rates, string $vehicleType): void
    {
        foreach ($rates as $rate) {
            $rate['vehicle_type'] = $vehicleType;
            $company->rates()->updateOrCreate(
                [
                    'category' => $rate['category'],
                    'item_name' => $rate['item_name'],
                    'vehicle_type' => $vehicleType,
                ],
                $rate
            );
        }
    }
}
