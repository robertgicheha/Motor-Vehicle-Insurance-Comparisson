@php
    // Image-backed hero matching the style used on motor-private.blade.php /
    // motor-psv.blade.php etc. Distinct from partials/public-hero.blade.php
    // (the blue-gradient quotation hero), which stays untouched since it's
    // still used by layouts/public.blade.php.
    $heroEyebrow = $heroEyebrow ?? 'MIC Global Risks';
    $heroTitle = $heroTitle ?? '';
    $heroSubtitle = $heroSubtitle ?? '';
    $heroImage = $heroImage ?? 'images/hero/vehicle.jpg';
@endphp
<section class="page-hero">
  <div class="page-hero-content">
    <div class="page-hero-eyebrow">{{ $heroEyebrow }}</div>
    <h1>{{ $heroTitle }}</h1>
    @if($heroSubtitle)
      <p>{{ $heroSubtitle }}</p>
    @endif
    @isset($heroCta)
      <a href="{{ $heroCta['url'] }}" class="btn-hero">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
        {{ $heroCta['label'] }}
      </a>
    @endisset
  </div>
</section>
