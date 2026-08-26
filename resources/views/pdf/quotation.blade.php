<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 0; }
        .letterhead {
            display: flex; justify-content: space-between; align-items: stretch; min-height: 90px;
        }
        .lh-left {
            display: flex; align-items: center; padding: 20px 28px; min-width: 220px;
        }
        .lh-left img { max-height: 44px; max-width: 160px; object-fit: contain; }
        .lh-left .lh-name { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: .01em; display: none; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 18px 28px; text-align: right; }
        .lh-right p { font-size: 12px; line-height: 1.75; color: rgba(255,255,255,.92); margin: 0; }
        .pdf-body { padding: 28px 36px; }
        .pdf-body h2 { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        .pdf-body .pdf-date { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        .pdf-body p { font-size: 13px; line-height: 1.7; margin-bottom: 16px; }
        .pdf-body table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
        .pdf-body table th { background: #f0f4fa; padding: 8px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; border: 1px solid #dde3ed; }
        .pdf-body table td { padding: 9px 12px; border: 1px solid #dde3ed; }
        .pdf-footer-text {
            padding: 20px 36px; background: #f8f9fb; border-top: 1px solid #dde3ed;
            font-size: 11px; color: #6b7280; line-height: 1.8;
        }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #0066B3; margin: 24px 0 10px; padding-bottom: 8px; border-bottom: 1.5px solid #e8f2fa; }
        .total-row { background: #004f8c; color: white; font-weight: bold; }
        .total-row td { padding: 10px 12px; font-size: 14px; }
        .company-logo { max-height: 40px; max-width: 140px; }
    </style>
</head>
<body>
    @php
        $company = $quotation->company;
        $slug = strtoupper($company->slug ?? '');
        $companyMeta = [
            'GA' => [
                'color' => '#005baa',
                'address' => 'GA Insurance Ltd, GA Building, Hospital Road, Upper Hill, P.O. Box 42166 - 00100 Nairobi',
                'website' => 'www.gainsurance.co.ke',
                'img' => asset('images/GA Insurance.png'),
            ],
            'BRITAM' => [
                'color' => '#0071bc',
                'address' => 'Britam General Insurance Company (Kenya) Limited, Britam Centre, Junction of Mara and Ragati Road, Upper Hill, P.O. BOX 30375 - 0100 Nairobi',
                'website' => 'www.britam.com',
                'img' => asset('images/britam_logo.png'),
            ],
            'SANLAM' => [
                'color' => '#1d3c6e',
                'address' => 'Sanlam Allianz Insurance Company Limited, Sanlam Tower, Hospital Road, Upper Hill, P.O. Box 30522 - 00100 Nairobi',
                'website' => 'www.sanlam.co.ke',
                'img' => asset('images/Sanlam-Allianz.png'),
            ],
        ];
        $meta = $companyMeta[$slug] ?? ['color' => '#0066B3', 'address' => '', 'website' => 'www.micglobalrisks.com', 'img' => $company->logoUrl()];
        $now = now()->format('d F Y, h:i A');
        $customerName = $quotation->user->name ?? 'N/A';
    @endphp

    {{-- LETTERHEAD BANNER --}}
    <div class="letterhead" style="background:{{ $meta['color'] }}">
        <div class="lh-left">
            @php $__logoPath = $company->logo ? public_path('storage/' . $company->logo) : (isset(\App\Models\Company::hardcodedLogoMap()[$company->slug]) ? public_path(\App\Models\Company::hardcodedLogoMap()[$company->slug]) : ''); @endphp
            @if($__logoPath && file_exists($__logoPath))
                <img src="{{ $__logoPath }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @else
                <img src="{{ $meta['img'] ?? '' }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @endif
            <span class="lh-name">{{ $company->name }}</span>
        </div>
        <div class="lh-right">
            <p>{{ $meta['address'] }}<br>Website: {{ $meta['website'] }}</p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="pdf-body">
        <p style="margin-top:4px">{{ $now }}</p>
        <p>Dear {{ $customerName }},</p>
        <h2>RE: PRIVATE CAR INSURANCE QUOTE</h2>
        <p class="pdf-date">Quotation Reference: <strong>{{ $quotation->quotation_number }}</strong></p>
        <p>Thank you for requesting us to provide a quote for the above mentioned. We are pleased to submit the quote</p>

        <table>
            <thead>
                <tr><th>Owner's Name</th><th>Vehicle Make</th><th>Year of Manufacture</th></tr>
            </thead>
            <tbody>
                <tr><td>{{ $customerName }}</td><td>{{ $quotation->car_make }} {{ $quotation->car_model }}</td><td>{{ $quotation->car_year }}</td></tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr><th>Registration Number</th><th>Vehicle Estimated Price</th><th>Phone Number</th></tr>
            </thead>
            <tbody>
                <tr><td>{{ $quotation->car_registration ?? '—' }}</td><td>KES {{ number_format($quotation->car_valuation, 2) }}</td><td>{{ $quotation->user->mobile ?? 'N/A' }}</td></tr>
            </tbody>
        </table>

        <table>
            <thead><tr><th colspan="2">COVER TYPE</th></tr></thead>
            <tbody>
                <tr><td>Scope of Cover</td><td>Comprehensive {{ ucfirst($quotation->vehicle_type ?? 'private') }}</td></tr>
                <tr><td>Frequency</td><td>Annually</td></tr>
            </tbody>
        </table>

        @php
            $__companyRates = $quotation->company->rates()
                ->where('vehicle_type', $quotation->vehicle_type ?? 'private')
                ->get()
                ->groupBy('category');

            $_breakdown = $quotation->breakdown ?? [];
            $_premiumRate = $_breakdown['premium_rate'] ?? 6.5;
            $_pvtRate = $_breakdown['pvt_rate'] ?? 0;
            $_levisRate = $_breakdown['levis_rate'] ?? 0.0045;
            $_stampDuty = $_breakdown['stamp_duty'] ?? 40;
            $_pvtPremium = $quotation->pvt_premium ?? 0;
            $_pllPerPerson = $_breakdown['pll_per_person'] ?? 0;
            $_numPassengers = $quotation->num_passengers ?? 1;
            $_pllTotal = $_pllPerPerson * $_numPassengers;
            $_levisAmount = ($quotation->premium + $_pvtPremium + $_pllTotal) * $_levisRate;

            function fmtRateValue($r) {
                $parts = [];
                if ($r->percentage && $r->percentage > 0) $parts[] = $r->percentage . '%';
                if ($r->value && $r->value > 0) $parts[] = 'Kes ' . number_format($r->value, 0);
                if ($r->min_value && $r->min_value > 0) $parts[] = 'Min Kes ' . number_format($r->min_value, 0);
                if ($r->max_value && $r->max_value > 0) $parts[] = 'Max Kes ' . number_format($r->max_value, 0);
                if ($r->min_premium && $r->min_premium > 0) $parts[] = 'Min prem Kes ' . number_format($r->min_premium, 0);
                return implode(', ', $parts) ?: ($r->notes ? substr($r->notes, 0, 60) : '');
            }
        @endphp

        @php
            $_premRateItems = $__companyRates->get('Premium Rate');
            $_excessODCalc = $_breakdown['excess_own_damage'] ?? 0;
            $_excessTheftCalc = $_breakdown['excess_theft'] ?? 0;
            $_excessYoungCalc = $_breakdown['excess_young_driver'] ?? 0;
            $_pllPerPerson = $_breakdown['pll_per_person'] ?? 0;
            $_pllTotal = $_breakdown['pll_total'] ?? ($_pllPerPerson * $_numPassengers);
            $_premRateApplied = $_breakdown['premium_rate'] ?? $_premiumRate;
            $_basicPremium = $_breakdown['basic_premium'] ?? $quotation->premium;
            $_pvtRate = $_breakdown['pvt_rate'] ?? 0;
            $_pvtPremium = $_breakdown['pvt_premium'] ?? $quotation->pvt_premium;
            $_levisRate = $_breakdown['levis_rate'] ?? 0.0045;
            $_levisAmount = $_breakdown['levis'] ?? ($quotation->premium * $_levisRate);
            $_stampDuty = $_breakdown['stamp_duty'] ?? 40;
        @endphp

        {{-- ── PREMIUM & RATES BREAKDOWN ── --}}
        <div class="section-title">PREMIUM &amp; RATES BREAKDOWN</div>
        <table>
            <thead><tr><th>Component</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount (KES)</th></tr></thead>
            <tbody>
                <tr><td>Estimated Vehicle Value</td><td style="text-align:right">—</td><td style="text-align:right;font-weight:bold">{{ number_format($quotation->car_valuation, 2) }}</td></tr>
                <tr><td>Basic Annual Premium</td><td style="text-align:right">{{ number_format($_premRateApplied, 2) }}%</td><td style="text-align:right">{{ number_format($_basicPremium, 2) }}</td></tr>
                @if($_pvtPremium > 0 || $_pvtRate > 0)
                <tr><td>PVT (Passenger Vehicle Tax) Premium</td><td style="text-align:right">{{ number_format($_pvtRate, 2) }}%</td><td style="text-align:right">{{ number_format($_pvtPremium, 2) }}</td></tr>
                @endif
                @if($_pllTotal > 0)
                <tr><td>PLL Premium ({{ $_numPassengers }} pax)</td><td style="text-align:right">KES {{ number_format($_pllPerPerson, 0) }}/pax</td><td style="text-align:right">{{ number_format($_pllTotal, 2) }}</td></tr>
                @endif
                <tr><td>IRA Training Levy</td><td style="text-align:right">{{ number_format($_levisRate * 100, 2) }}%</td><td style="text-align:right">{{ number_format($_levisAmount, 2) }}</td></tr>
                <tr><td>Stamp Duty</td><td style="text-align:right">Flat</td><td style="text-align:right">{{ number_format($_stampDuty, 2) }}</td></tr>
                @if($_excessODCalc > 0)
                <tr><td>Excess - Own Damage</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($_excessODCalc, 2) }}</td></tr>
                @endif
                @if($_excessTheftCalc > 0)
                <tr><td>Excess - Theft</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($_excessTheftCalc, 2) }}</td></tr>
                @endif
                @if($_excessYoungCalc > 0)
                <tr><td>Excess - Novice &amp; Young Drivers</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($_excessYoungCalc, 2) }}</td></tr>
                @endif
            </tbody>
        </table>

        @if($_premRateItems && $_premRateItems->isNotEmpty())
        <div class="section-title">APPLICABLE RATES</div>
        <table>
            <thead><tr><th>Rate Item</th><th style="text-align:right">Rate / Value</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($_premRateItems as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right;font-weight:600">{{ fmtRateValue($r) ?: '—' }}</td>
                    <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        <div class="section-title">SCHEDULE OF INSURANCE</div>
        <table>
            <thead><tr><th>Description</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount (KES)</th></tr></thead>
            <tbody>
                <tr><td>Estimated Vehicle Value</td><td style="text-align:right">—</td><td style="text-align:right;font-weight:bold">{{ number_format($quotation->car_valuation, 2) }}</td></tr>
                <tr><td>Basic Annual Premium</td><td style="text-align:right">{{ number_format($_premRateApplied, 2) }}%</td><td style="text-align:right">{{ number_format($quotation->premium, 2) }}</td></tr>
                @if($_pvtPremium > 0 || $_pvtRate > 0)
                <tr><td>PVT Premium</td><td style="text-align:right">{{ number_format($_pvtRate, 2) }}%</td><td style="text-align:right">{{ number_format($_pvtPremium, 2) }}</td></tr>
                @endif
                @if($_pllTotal > 0)
                <tr><td>PLL Premium ({{ $_numPassengers }} pax)</td><td style="text-align:right">KES {{ number_format($_pllPerPerson, 0) }}/pax</td><td style="text-align:right">{{ number_format($_pllTotal, 2) }}</td></tr>
                @endif
                <tr><td>IRA Training Levy</td><td style="text-align:right">{{ number_format($_levisRate * 100, 2) }}%</td><td style="text-align:right">{{ number_format($_levisAmount, 2) }}</td></tr>
                <tr><td>Stamp Duty</td><td style="text-align:right">Flat</td><td style="text-align:right">{{ number_format($_stampDuty, 2) }}</td></tr>
                @if($quotation->excess_own_damage)
                    <tr><td>Excess - Own Damage</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($quotation->excess_own_damage, 2) }}</td></tr>
                @endif
                @if($quotation->excess_theft)
                    <tr><td>Excess - Theft</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($quotation->excess_theft, 2) }}</td></tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row"><td>Total Premium (incl. taxes)</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($quotation->total_premium, 2) }}</td></tr>
            </tfoot>
        </table>

        @if($__companyRates->has('Cover Limits'))
        <div class="section-title">BENEFITS / COVER LIMITS</div>
        <table>
            <thead><tr><th>Benefit</th><th style="text-align:right">Limit (KES)</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Cover Limits') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ? substr($r->notes, 0, 30) : '—') }}</td>
                    <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($__companyRates->has('Extras'))
        <div class="section-title">EXTRAS</div>
        <table>
            <thead><tr><th>Extra</th><th style="text-align:right">Limit (KES)</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Extras') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : '—' }}</td>
                    <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($__companyRates->has('Third Party Limits'))
        <div class="section-title">THIRD PARTY LIMITS OF LIABILITY</div>
        <table>
            <thead><tr><th>Cover</th><th style="text-align:right">Limit (KES)</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Third Party Limits') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ?? '—') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($__companyRates->has('Excess'))
        <div class="section-title">POLICY EXCESSES</div>
        <table>
            <thead><tr><th>Excess Type</th><th style="text-align:right">Excess Amount</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Excess') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ fmtRateValue($r) ?: '—' }}</td>
                    <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($__companyRates->has('Additional Cover'))
        <div class="section-title">SPECIAL CONDITIONS / ADDITIONAL COVER</div>
        <table>
            <thead><tr><th>Condition</th><th>Status / Details</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Additional Cover') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="font-size:10px">{{ $r->notes ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($__companyRates->has('Optional Benefits'))
        <div class="section-title">OPTIONAL BENEFITS</div>
        <table>
            <thead><tr><th>Benefit</th><th style="text-align:right">Premium (KES)</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($__companyRates->get('Optional Benefits') as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : '—' }}</td>
                    <td style="font-size:9px;color:#64748b">{{ $r->notes ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @php
            $_maxAgeRatePDF = $__companyRates->get('Premium Rate')->firstWhere('item_name', 'Max Age New Business');
            $_excessProtectorPDF = $__companyRates->get('Additional Cover') ? $__companyRates->get('Additional Cover')->firstWhere('item_name', 'Excess Protector') : null;
            $_minSumInsuredPDF = $quotation->vehicle_type === 'private' ? $quotation->company->min_sum_insured_private : $quotation->company->min_sum_insured_psv;
            $_minSumInsuredRatePDF = $__companyRates->get('Premium Rate')->firstWhere('item_name', 'Minimum Sum Insured');
            $_geoAreaPDF = $__companyRates->get('Cover Limits') ? $__companyRates->get('Cover Limits')->firstWhere('item_name', 'Geographical Area') : null;
        @endphp

        <div class="section-title">ADDITIONAL INFO</div>
        <table>
            <tbody>
                <tr><td style="width:40%;font-weight:bold">Minimum Sum Insured</td><td>{{ $_minSumInsuredPDF ? 'KES ' . number_format($_minSumInsuredPDF, 0) : ($_minSumInsuredRatePDF?->notes ?? '—') }}</td></tr>
                <tr><td style="font-weight:bold">Max Age New Business</td><td>{{ $_maxAgeRatePDF ? $_maxAgeRatePDF->value . ' years' : '—' }}</td></tr>
                @if($_excessProtectorPDF)
                <tr><td style="font-weight:bold">Excess Protector</td><td style="font-size:10px">{{ $_excessProtectorPDF->notes ?? '—' }}</td></tr>
                @endif
                @if($_geoAreaPDF)
                <tr><td style="font-weight:bold">Geographical Area</td><td style="font-size:10px">{{ $_geoAreaPDF->notes ?? ($_geoAreaPDF->value > 0 ? 'KES ' . number_format($_geoAreaPDF->value, 0) : '—') }}</td></tr>
                @endif
            </tbody>
        </table>

        <p style="font-size:10px;color:#64748b;margin-top:15px">
            The Comprehensive cover is subject to a satisfactory valuation report conducted within the first month of cover. Subject to sanction limitation and exclusion clause and standard policy terms and conditions.
        </p>
        <p style="font-size:10px;color:#64748b">
            Geographical limit: The geographical area is specified in the Special Conditions above.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="pdf-footer-text">
        KENYA | UGANDA | TANZANIA | Rate subject to satisfactory valuation report<br>
        Quotation {{ $quotation->quotation_number }} | This is a computer-generated document.
    </div>
</body>
</html>
