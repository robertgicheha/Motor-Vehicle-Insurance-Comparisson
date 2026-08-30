<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The combined 0.45% "Levis Rate" is replaced by its two regulatory
     * components so they can be shown and edited separately: Training Levy
     * (0.2%) and Policy Holder Fund (0.25%) — 0.2 + 0.25 = 0.45, so total
     * premiums are unaffected.
     */
    public function up(): void
    {
        $levisRates = DB::table('rates')
            ->where('category', 'Premium Rate')
            ->where('item_name', 'Levis Rate')
            ->get();

        foreach ($levisRates as $rate) {
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

            DB::table('rates')->where('id', $rate->id)->delete();
        }
    }

    public function down(): void
    {
        $companies = DB::table('rates')
            ->where('category', 'Premium Rate')
            ->whereIn('item_name', ['Training Levy Rate', 'Policy Holder Fund Rate'])
            ->get(['company_id', 'vehicle_type', 'value_type'])
            ->unique(fn ($r) => $r->company_id . '::' . $r->vehicle_type);

        foreach ($companies as $row) {
            DB::table('rates')->insert([
                'company_id' => $row->company_id,
                'category' => 'Premium Rate',
                'item_name' => 'Levis Rate',
                'vehicle_type' => $row->vehicle_type,
                'value_type' => $row->value_type,
                'percentage' => 0.45,
                'notes' => '0.45% of (PVT + PLL)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('rates')
            ->where('category', 'Premium Rate')
            ->whereIn('item_name', ['Training Levy Rate', 'Policy Holder Fund Rate'])
            ->delete();
    }
};
