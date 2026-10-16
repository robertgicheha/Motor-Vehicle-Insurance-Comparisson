@php
    // Reusable public-page hero — defaults reproduce the original hardcoded
    // "Motor Insurance Quotation" banner exactly, so layouts/public.blade.php's
    // existing usage (stickers/generate.blade.php) is unaffected. Pass
    // $heroTitle / $heroSubtitle / $heroBreadcrumb / $heroShowRef to reuse it
    // for other public pages instead of hardcoding a near-duplicate hero.
    $heroBreadcrumb = $heroBreadcrumb ?? [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Motor Quote', 'url' => url('/motor-quote')],
        ['label' => 'Quotation'],
    ];
    $heroTitle = $heroTitle ?? 'Motor Insurance Quotation';
    $heroSubtitle = $heroSubtitle ?? "Your personalised premium breakdown from Kenya's leading motor insurers. Review, compare and download your quotation below.";
    $heroShowRef = $heroShowRef ?? true;
@endphp
<section class="bg-gradient-to-r from-[#263B8B] via-[#0066B3] to-[#0091d5] px-10 py-[52px] text-white relative overflow-hidden">
  <div class="relative z-10 max-w-[1100px] mx-auto">
    <div class="flex items-center gap-2 text-[12px] text-white/65 mb-[18px]">
      @foreach($heroBreadcrumb as $i => $crumb)
        @if($i > 0)<span class="opacity-50">&rsaquo;</span>@endif
        @if(!empty($crumb['url']))
          <a href="{{ $crumb['url'] }}" class="text-white/75 hover:text-white no-underline py-1 inline-block">{{ $crumb['label'] }}</a>
        @else
          <span>{{ $crumb['label'] }}</span>
        @endif
      @endforeach
    </div>
    <h1 class="text-3xl font-extrabold tracking-tight mb-2">{{ $heroTitle }}</h1>
    <p class="text-sm text-white/75 max-w-[520px]">{{ $heroSubtitle }}</p>
    @if($heroShowRef)
    <div class="inline-flex items-center gap-2 bg-white/15 border border-white/25 rounded-full px-3.5 py-1 text-[12.5px] font-bold tracking-wide mt-4">
      <span class="opacity-70 font-medium">Ref:</span>
      <span id="hero-quote-num">&mdash;</span>
    </div>
    @endif
  </div>
</section>
