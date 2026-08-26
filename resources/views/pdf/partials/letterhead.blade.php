@php
    $__company = $company ?? null;
    $__companyMeta = [
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
    $__slug = $__company ? strtoupper($__company->slug ?? '') : '';
    if ($__company) {
        $__meta = $__companyMeta[$__slug] ?? [
            'color' => '#0066B3',
            'address' => $__company->name . ', Nairobi, Kenya',
            'website' => 'www.micglobalrisks.com',
            'img' => $__company->logoUrl(),
        ];
    } else {
        $__meta = [
            'color' => '#004f8c',
            'address' => 'MIC Global Risks (Kenya) Limited, P.O. Box 10000 - 00100 Nairobi, Kenya',
            'website' => 'www.micglobalrisks.com',
            'img' => asset('images/MIC-Global-Risks_logo_no_background.png'),
        ];
    }
    $__brand = $__company?->name ?? 'MIC Global Risks';
@endphp
<div class="letterhead" style="background:{{ $__meta['color'] }}">
    <div class="lh-left">
        @php $__logoPath = $__company && $__company->logo ? public_path('storage/' . $__company->logo) : (isset(\App\Models\Company::hardcodedLogoMap()[$__slug]) ? public_path(\App\Models\Company::hardcodedLogoMap()[$__slug]) : ''); @endphp
        @if($__company && $__logoPath && file_exists($__logoPath))
            <img src="{{ $__logoPath }}" alt="{{ $__brand }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        @else
            <img src="{{ $__meta['img'] }}" alt="{{ $__brand }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        @endif
        <span class="lh-name">{{ $__brand }}</span>
    </div>
    <div class="lh-right">
        <p>{{ $__meta['address'] }}<br>Website: {{ $__meta['website'] }}</p>
    </div>
</div>
