@php
    $__company = $company ?? null;
    $__slug = strtoupper($__company->slug ?? '');
    $__companyMeta = [
        'GA' => [
            'address' => 'GA Insurance Ltd, GA Building, Hospital Road, Upper Hill, P.O. Box 42166 - 00100 Nairobi',
            'img' => asset('images/GA Insurance.png'),
        ],
        'BRITAM' => [
            'address' => 'Britam General Insurance Company (Kenya) Limited, Britam Centre, Junction of Mara and Ragati Road, Upper Hill, P.O. BOX 30375 - 0100 Nairobi',
            'img' => asset('images/britam_logo.png'),
        ],
        'SANLAM' => [
            'address' => 'Sanlam Allianz Insurance Company Limited, Sanlam Tower, Hospital Road, Upper Hill, P.O. Box 30522 - 00100 Nairobi',
            'img' => asset('images/Sanlam-Allianz.png'),
        ],
    ];
    $__meta = $__companyMeta[$__slug] ?? [
        'address' => $__company ? $__company->name . ', Nairobi, Kenya' : 'LOLC Microfinance Bank, P.O. Box 10000 - 00100 Nairobi, Kenya',
        'img' => $__company ? $__company->logoUrl() : asset('images/LOLC-Kenya.webp'),
    ];
    $__brand = $__company?->name ?? 'LOLC Microfinance Bank';
    $__logoPath = $__company ? ($__company->logoPath() ?? '') : '';
@endphp

{{-- LOLC MICROFINANCE BANK BANNER --}}
@php $__micLogoPath = public_path('images/LOLC-Kenya.webp'); @endphp
@if(file_exists($__micLogoPath))
<div class="mic-banner">
    <img src="{{ $__micLogoPath }}" alt="LOLC Microfinance Bank">
</div>
@endif

{{-- LETTERHEAD BANNER --}}
<div class="letterhead">
    <div class="lh-left">
        @if($__company && $__logoPath && file_exists($__logoPath))
            <img src="{{ $__logoPath }}" alt="{{ $__brand }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        @else
            <img src="{{ $__meta['img'] }}" alt="{{ $__brand }}" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
        @endif
        <span class="lh-fallback-name">{{ $__brand }}</span>
        <div class="lh-caption">
            <span class="lh-company-name">{{ $__brand }}</span>
        </div>
    </div>
    <div class="lh-right">
        <p>{{ $__meta['address'] }}</p>
    </div>
</div>
