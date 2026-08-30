<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #1a1a2e; margin: 0; padding: 0; }
        .mic-banner { text-align: center; padding: 6px 0 4px; }
        .mic-banner img { max-height: 36px; }
        .letterhead {
            display: flex; justify-content: space-between; align-items: stretch; min-height: 64px; border-bottom: 1.5px solid #dde3ed;
        }
        .lh-left {
            display: flex; flex-direction: column; align-items: flex-start; justify-content: center; padding: 8px 26px; min-width: 220px;
        }
        .lh-left img { max-height: 34px; max-width: 180px; object-fit: contain; }
        .lh-left .lh-fallback-name { font-size: 18px; font-weight: 800; color: #1a1a2e; letter-spacing: .01em; display: none; }
        .lh-left .lh-caption { margin-top: 4px; font-size: 10px; }
        .lh-left .lh-caption span { display: block; color: #6b7280; line-height: 1.4; }
        .lh-left .lh-caption .lh-company-name { font-weight: 700; color: #1a1a2e; }
        .lh-right { flex: 1; display: flex; align-items: center; justify-content: flex-end; padding: 8px 26px; text-align: right; }
        .lh-right p { font-size: 10px; line-height: 1.4; color: #6b7280; margin: 0; }
        .pdf-body { padding: 10px 26px; }
        .pdf-body h2 { font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; margin: 0; text-decoration: underline; display: inline-block; }
        .re-row { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 6px; }
        .date-ref-row { width: 100%; border-collapse: collapse; margin: 4px 0 5px; font-size: 10.5px; }
        .date-ref-row td { border: none; padding: 0; }
        .pdf-body p { font-size: 10.5px; line-height: 1.35; margin: 0 0 5px; }
        .pdf-body table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 10px; }
        .pdf-body table th { background: #f0f4fa; padding: 3px 8px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; border: 1px solid #dde3ed; }
        .pdf-body table td { padding: 3px 8px; border: 1px solid #dde3ed; }
        .pdf-footer-text {
            padding: 8px 26px; background: #f8f9fb; border-top: 1px solid #dde3ed;
            font-size: 9px; color: #6b7280; line-height: 1.4;
        }
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #1a1a2e; margin: 8px 0 3px; padding-bottom: 3px; border-bottom: 1.5px solid #dde3ed; }
        .total-row { background: #004f8c; color: white; font-weight: bold; }
        .total-row td { padding: 5px 8px; font-size: 11px; }
        .company-logo { max-height: 40px; max-width: 140px; }
        .grid-2col td { width: 25%; }
        .page-break { page-break-before: always; }
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
        $now = now()->format('d F Y');
        $customerName = $quotation->customer_name;
        $isPsv = strtoupper($quotation->vehicle_type ?? 'private') === 'PSV';

        // Any rate item whose notes literally say it isn't covered shouldn't be
        // printed at all — it's noise, not information a customer needs.
        $notCovered = fn($r) => $r->notes && stripos($r->notes, 'no cover') !== false;
    @endphp

    {{-- MIC GLOBAL RISKS BANNER --}}
    @php $__micLogoPath = public_path('images/MIC-Global-Risks_logo_no_background.png'); @endphp
    @if(file_exists($__micLogoPath))
    <div class="mic-banner">
        <img src="{{ $__micLogoPath }}" alt="MIC Global Risks">
    </div>
    @endif

    {{-- LETTERHEAD BANNER --}}
    <div class="letterhead">
        <div class="lh-left">
            @php $__logoPath = $company->logo ? public_path('storage/' . $company->logo) : (isset(\App\Models\Company::hardcodedLogoMap()[$company->slug]) ? public_path(\App\Models\Company::hardcodedLogoMap()[$company->slug]) : ''); @endphp
            @if($__logoPath && file_exists($__logoPath))
                <img src="{{ $__logoPath }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @else
                <img src="{{ $meta['img'] ?? '' }}" alt="{{ $company->name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
            @endif
            <span class="lh-fallback-name">{{ $company->name }}</span>
            <div class="lh-caption">
                <span class="lh-company-name">{{ $company->name }}</span>
            </div>
        </div>
        <div class="lh-right">
            <p>{{ $meta['address'] }}</p>
        </div>
    </div>

    {{-- BODY --}}
    <div class="pdf-body">
        <table class="date-ref-row">
            <tr>
                <td style="border:none;padding:0;text-align:left">Date: {{ $now }}</td>
                <td style="border:none;padding:0;text-align:right">Quotation No: <strong>{{ $quotation->quotation_number }}</strong></td>
            </tr>
        </table>
        <p>Dear {{ $customerName }},</p>
        <div class="re-row">
            <h2>RE: {{ $isPsv ? 'Motor PSV(Chauffer Driven) Insurance Quote' : 'Motor Private Insurance Quote' }}</h2>
        </div>

        <div class="section-title" style="margin-top:4px">Insured's Details</div>
        <table>
            <thead>
                <tr><th>Name</th><th>Phone Number</th></tr>
            </thead>
            <tbody>
                <tr><td>{{ $customerName }}</td><td>{{ $quotation->customer_phone ?? 'N/A' }}</td></tr>
            </tbody>
        </table>

        <div class="section-title">Vehicle Details</div>
        <table class="grid-2col">
            <tbody>
                <tr>
                    <td style="font-weight:bold;background:#f8f9fb">Registration No</td><td>{{ $quotation->car_registration ?: '—' }}</td>
                    <td style="font-weight:bold;background:#f8f9fb">Make of Vehicle</td><td>{{ $quotation->car_make ?: '—' }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;background:#f8f9fb">Model No</td><td>{{ $quotation->car_model ?: '—' }}</td>
                    <td style="font-weight:bold;background:#f8f9fb">Chassis No</td><td>{{ $quotation->chassis_number ?: '—' }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;background:#f8f9fb">Engine No</td><td>{{ $quotation->engine_number ?: '—' }}</td>
                    <td style="font-weight:bold;background:#f8f9fb">Year of Make</td><td>{{ $quotation->car_year ?: '—' }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold;background:#f8f9fb">Valuation of Vehicle</td><td>KES {{ number_format($quotation->car_valuation, 2) }}</td>
                    <td style="font-weight:bold;background:#f8f9fb">Number of Passengers</td><td>{{ $quotation->num_passengers ?? 1 }}</td>
                </tr>
            </tbody>
        </table>

        @php
            $__companyRates = $quotation->company->rates()
                ->where('vehicle_type', $quotation->vehicle_type ?? 'private')
                ->get()
                ->groupBy('category');

            // Recomputed fresh (not the frozen $quotation->breakdown JSON) so the
            // PDF always reflects the current rate configuration and formula —
            // e.g. an older quotation's stored breakdown predates the Training
            // Levy / Policy Holder Fund split and would otherwise show 0.00 for
            // both, even though the underwriter's rates have since been updated.
            $_breakdown = app(\App\Services\PremiumCalculationService::class)->calculate(
                $quotation->company,
                (float) $quotation->car_valuation,
                $quotation->vehicle_type ?? 'private',
                true,
                $quotation->num_passengers ?? 1,
                true,
                false
            );
            $_pllPerPerson = $_breakdown['pll_per_person'] ?? 0;
            $_numPassengers = $quotation->num_passengers ?? 1;
            $_pllTotal = $_breakdown['pll_total'] ?? ($_pllPerPerson * $_numPassengers);
            $_premRateApplied = $_breakdown['premium_rate'] ?? 6.5;
            $_pvtRate = $_breakdown['pvt_rate'] ?? 0;
            $_pvtPremium = $_breakdown['pvt_premium'] ?? $quotation->pvt_premium ?? 0;
            $_trainingLevyRate = $_breakdown['training_levy_rate'] ?? 0.2;
            $_trainingLevy = $_breakdown['training_levy'] ?? 0;
            $_policyHolderFundRate = $_breakdown['policy_holder_fund_rate'] ?? 0.25;
            $_policyHolderFund = $_breakdown['policy_holder_fund'] ?? 0;
            $_stampDuty = $_breakdown['stamp_duty'] ?? 40;
            $_excessOD = $_breakdown['excess_own_damage'] ?? 0;
            $_excessTheft = $_breakdown['excess_theft'] ?? 0;

            // A closure (not a bare function) — this @php block can run more than
            // once per request (e.g. this PDF gets rendered twice when the NAV
            // email path renders it directly and the Mail fallback also renders
            // it via the Mailable), and PHP cannot redeclare a top-level function.
            $fmtRateValue = function ($r) {
                $parts = [];
                if ($r->percentage && $r->percentage > 0) $parts[] = \App\Models\Rate::fmtPercent($r->percentage) . '%';
                if ($r->value && $r->value > 0) $parts[] = 'Kes ' . number_format($r->value, 0);
                if ($r->min_value && $r->min_value > 0) $parts[] = 'Min Kes ' . number_format($r->min_value, 0);
                if ($r->max_value && $r->max_value > 0) $parts[] = 'Max Kes ' . number_format($r->max_value, 0);
                if ($r->min_premium && $r->min_premium > 0) $parts[] = 'Min prem Kes ' . number_format($r->min_premium, 0);
                return implode(', ', $parts) ?: ($r->notes ? substr($r->notes, 0, 60) : '');
            };
        @endphp

        <div class="section-title">Premium Total</div>
        <table>
            <thead><tr><th>Description</th><th style="text-align:right">Rate</th><th style="text-align:right">Amount (KES)</th></tr></thead>
            <tbody>
                <tr><td>Estimated Vehicle Value</td><td style="text-align:right">—</td><td style="text-align:right;font-weight:bold">{{ number_format($quotation->car_valuation, 2) }}</td></tr>
                <tr><td>Basic Annual Premium</td><td style="text-align:right">{{ \App\Models\Rate::fmtPercent($_premRateApplied) }}%</td><td style="text-align:right">{{ number_format($_breakdown['basic_premium'] ?? $quotation->premium, 2) }}</td></tr>
                @if($_pvtPremium > 0 || $_pvtRate > 0)
                <tr><td>PVT(Political Violence &amp; Terrorism Extension)</td><td style="text-align:right">{{ \App\Models\Rate::fmtPercent($_pvtRate) }}%</td><td style="text-align:right">{{ number_format($_pvtPremium, 2) }}</td></tr>
                @endif
                @if($_pllTotal > 0)
                <tr><td>PLL Premium ({{ $_numPassengers }} pax)</td><td style="text-align:right">KES {{ number_format($_pllPerPerson, 0) }}/pax</td><td style="text-align:right">{{ number_format($_pllTotal, 2) }}</td></tr>
                @endif
                <tr><td>Training Levy</td><td style="text-align:right">{{ \App\Models\Rate::fmtPercent($_trainingLevyRate) }}%</td><td style="text-align:right">{{ number_format($_trainingLevy, 2) }}</td></tr>
                <tr><td>Policy Holder Fund</td><td style="text-align:right">{{ \App\Models\Rate::fmtPercent($_policyHolderFundRate) }}%</td><td style="text-align:right">{{ number_format($_policyHolderFund, 2) }}</td></tr>
                <tr><td>Stamp Duty</td><td style="text-align:right">-</td><td style="text-align:right">{{ number_format($_stampDuty, 2) }}</td></tr>
                <tr><td>Excess - Own Damage</td><td style="text-align:right">—</td><td style="text-align:right">{{ $_excessOD > 0 ? number_format($_excessOD, 2) : 'Not Applicable' }}</td></tr>
                <tr><td>Excess - Theft</td><td style="text-align:right">—</td><td style="text-align:right">{{ $_excessTheft > 0 ? number_format($_excessTheft, 2) : 'Not Applicable' }}</td></tr>
            </tbody>
            <tfoot>
                <tr class="total-row"><td>Total Premium (incl. taxes)</td><td style="text-align:right">—</td><td style="text-align:right">{{ number_format($_breakdown['total_premium'] ?? $quotation->total_premium, 2) }}</td></tr>
            </tfoot>
        </table>

        @php
            $_benefitsItems = $__companyRates->get('Cover Limits', collect())
                ->merge($__companyRates->get('Extras', collect()))
                ->reject(fn($r) => $r->item_name === 'Geographical Area')
                ->reject($notCovered);
            $_thirdPartyItems = $__companyRates->get('Third Party Limits', collect())->reject($notCovered);
            $_excessItems = $__companyRates->get('Excess', collect())->reject($notCovered);
            $_specialConditionItems = $__companyRates->get('Additional Cover', collect())->reject($notCovered);
        @endphp

        <div class="page-break"></div>

        @if($_benefitsItems->isNotEmpty())
        <div class="section-title">BENEFITS / COVER LIMITS</div>
        <table>
            <thead><tr><th>Benefit</th><th style="text-align:right">Limit (KES)</th></tr></thead>
            <tbody>
            @foreach($_benefitsItems as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ?: '—') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($_thirdPartyItems->isNotEmpty())
        <div class="section-title">THIRD PARTY LIMITS OF LIABILITY</div>
        <table>
            <thead><tr><th>Cover</th><th style="text-align:right">Limit (KES)</th></tr></thead>
            <tbody>
            @foreach($_thirdPartyItems as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $r->value > 0 ? number_format($r->value, 0) : ($r->notes ?: '—') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($_excessItems->isNotEmpty())
        <div class="section-title">POLICY EXCESSES</div>
        <table>
            <thead><tr><th>Excess Type</th><th style="text-align:right">Excess Amount</th></tr></thead>
            <tbody>
            @foreach($_excessItems as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right">{{ $fmtRateValue($r) ?: 'Not Applicable' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @if($_specialConditionItems->isNotEmpty())
        <div class="section-title">SPECIAL CONDITIONS</div>
        <table>
            <thead><tr><th>Condition</th><th style="text-align:right">Status / Details</th></tr></thead>
            <tbody>
            @foreach($_specialConditionItems as $r)
                <tr>
                    <td>{{ $r->item_name }}</td>
                    <td style="text-align:right;font-size:10px">{{ $r->notes ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        @php
            $_maxAgeRatePDF = $__companyRates->get('Premium Rate')->firstWhere('item_name', 'Max Age New Business');
            $_excessProtectorPDF = $__companyRates->get('Additional Cover') ? $__companyRates->get('Additional Cover')->firstWhere('item_name', 'Excess Protector') : null;
            if ($_excessProtectorPDF && $notCovered($_excessProtectorPDF)) { $_excessProtectorPDF = null; }
            $_minSumInsuredPDF = $quotation->vehicle_type === 'private' ? $quotation->company->min_sum_insured_private : $quotation->company->min_sum_insured_psv;
            $_minSumInsuredRatePDF = $__companyRates->get('Premium Rate')->firstWhere('item_name', 'Minimum Sum Insured');
            $_geoAreaPDF = $__companyRates->get('Cover Limits') ? $__companyRates->get('Cover Limits')->firstWhere('item_name', 'Geographical Area') : null;
            $_optionalBenefitsPDF = $__companyRates->get('Optional Benefits', collect())->reject($notCovered);
        @endphp

        <div class="section-title">ADDITIONAL INFO</div>
        <table>
            <tbody>
                <tr><td style="width:40%;font-weight:bold">Minimum Sum Insured</td><td style="text-align:right">{{ $_minSumInsuredPDF ? 'KES ' . number_format($_minSumInsuredPDF, 0) : ($_minSumInsuredRatePDF?->notes ?? '—') }}</td></tr>
                <tr><td style="font-weight:bold">Max Age New Business</td><td style="text-align:right">{{ $_maxAgeRatePDF ? number_format($_maxAgeRatePDF->value, 0) . ' Years' : '—' }}</td></tr>
                @if($_excessProtectorPDF)
                <tr><td style="font-weight:bold">Excess Protector</td><td style="text-align:right;font-size:10px">{{ $_excessProtectorPDF->notes ?? '—' }}</td></tr>
                @endif
                @if($_geoAreaPDF)
                <tr><td style="font-weight:bold">Geographical Area</td><td style="text-align:right;font-size:10px">{{ $_geoAreaPDF->notes ?? ($_geoAreaPDF->value > 0 ? 'KES ' . number_format($_geoAreaPDF->value, 0) : '—') }}</td></tr>
                @endif
                @foreach($_optionalBenefitsPDF as $r)
                <tr><td style="font-weight:bold">{{ $r->item_name }}</td><td style="text-align:right;font-size:10px">{{ $r->notes ?: ($r->value > 0 ? 'KES ' . number_format($r->value, 0) : '—') }}</td></tr>
                @endforeach
            </tbody>
        </table>

        <p style="font-size:9.5px;color:#64748b;margin-top:10px">
            Geographical limit: The geographical area is specified in the Special Conditions above.
        </p>
    </div>

    {{-- FOOTER --}}
    <div class="pdf-footer-text">
        Quotation {{ $quotation->quotation_number }} | This is a computer-generated document.
    </div>
</body>
</html>
