<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The 2026_08_28_172446 migration already split the combined 0.45%
     * "Levis Rate" into Training Levy Rate (0.2%) + Policy Holder Fund Rate
     * (0.25%), but CompanyRatesSeeder was never updated to match — it still
     * upserts a "Levis Rate" row keyed on (category, item_name,
     * vehicle_type), so any later reseed (e.g. a fresh deploy) silently
     * recreated the old combined rows. PremiumCalculationService only ever
     * looks up the two split item names, so those regenerated "Levis Rate"
     * rows were invisible to it — every PDF/backend premium total silently
     * dropped the levy (~0.45% of basic+PVT+PLL) while the client-side
     * quote-generation display, which still hardcodes the same 0.45%,
     * did not. This redoes the same split, scoped only to whatever
     * "Levis Rate" rows exist right now, so it's safe to run regardless of
     * whether the original migration's effect is still present.
     */
    public function up(): void
    {
        $levisRates = DB::table('rates')
            ->where('category', 'Premium Rate')
            ->where('item_name', 'Levis Rate')
            ->get();

        foreach ($levisRates as $rate) {
            $alreadySplit = DB::table('rates')
                ->where('company_id', $rate->company_id)
                ->where('category', 'Premium Rate')
                ->where('vehicle_type', $rate->vehicle_type)
                ->whereIn('item_name', ['Training Levy Rate', 'Policy Holder Fund Rate'])
                ->exists();

            if (!$alreadySplit) {
                DB::table('rates')->insert([
                    [
                        'company_id' => $rate->company_id,
                        'category' => 'Premium Rate',
                        'item_name' => 'Training Levy Rate',
                        'vehicle_type' => $rate->vehicle_type,
                        'value_type' => $rate->value_type,
                        'percentage' => 0.2,
                        'notes' => 'Training Levy — 0.2% of the premium',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'company_id' => $rate->company_id,
                        'category' => 'Premium Rate',
                        'item_name' => 'Policy Holder Fund Rate',
                        'vehicle_type' => $rate->vehicle_type,
                        'value_type' => $rate->value_type,
                        'percentage' => 0.25,
                        'notes' => 'Policy Holder Fund — 0.25% of the premium',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            DB::table('rates')->where('id', $rate->id)->delete();
        }
    }

    public function down(): void
    {
        // Intentionally a no-op: reverting would re-introduce the exact
        // regression this migration fixes. Roll back
        // 2026_08_28_172446_split_levis_rate_into_training_levy_and_policy_holder_fund
        // instead if the split itself needs to be undone.
    }
};
