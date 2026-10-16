<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Look Up Sticker | LOLC Microfinance Bank</title>
<meta name="description" content="Look up your motor insurance quotation by quotation number, registration number or name, and generate your insurance sticker.">
<link rel="icon" type="image/x-icon" href="{{ asset('Favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  [x-cloak]{display:none!important}
  .page-hero{position:relative;min-height:280px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(105deg,rgba(0,30,80,.9),rgba(0,60,130,.75)),url('{{ asset('images/hero/family-car.jpg') }}') center/cover}
  .page-hero-content{position:relative;z-index:2;padding:56px 48px;max-width:700px}
  .page-hero-eyebrow{color:#93c5fd;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;margin-bottom:8px}
  .page-hero h1{color:#fff;font-size:30px;font-weight:700;line-height:1.2;margin-bottom:10px}
  .page-hero p{color:rgba(255,255,255,.8);font-size:15px;line-height:1.6}
  .page-section{padding:56px 40px;max-width:900px;margin:0 auto}
  .section-label{color:var(--blue);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;text-align:center}
  .section-title{font-size:24px;font-weight:700;color:var(--text);margin-bottom:10px;text-align:center}
  .section-sub{font-size:14px;color:var(--text-muted);text-align:center;max-width:560px;margin:0 auto 32px}
  @media(max-width:768px){.page-hero-content{padding:36px 24px}.page-section{padding:36px 20px}}
</style>
</head>
<body>

<x-public-header/>

<section class="page-hero">
  <div class="page-hero-content">
    <div class="page-hero-eyebrow">Motor Insurance</div>
    <h1>Look Up Your Quotation &amp; Sticker</h1>
    <p>Search by your quotation number, vehicle registration, or name to find your quotation, download an issued sticker, or generate a new one.</p>
  </div>
</section>

<section class="page-section" x-data="stickerLookup()">

    @if(session('error'))
        <div class="mb-6 px-5 py-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="section-label">Find Your Quotation</div>
    <h2 class="section-title">Search for Your Quotation</h2>
    <p class="section-sub">Enter your quotation number, vehicle registration number, or full name to find your quotation.</p>

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Quotation No, Registration No, or Name</label>
                <input type="text" x-model="number" @keydown.enter="search"
                       placeholder="e.g. MIC-2026/1234, KDH 291H, or John Doe"
                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
            </div>
            <button @click="search" :disabled="!number || loading"
                    class="px-6 py-3 rounded-xl text-[13px] font-bold text-white bg-[#0066B3] hover:bg-[#263B8B] disabled:opacity-50 disabled:cursor-not-allowed transition shrink-0">
                <span x-show="!loading">Search</span>
                <span x-show="loading" class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Searching...
                </span>
            </button>
        </div>
    </div>

    <div x-show="error" x-cloak class="mt-5 bg-white border border-red-200 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center gap-2 text-sm font-medium text-red-600">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span x-text="error"></span>
        </div>
    </div>

    <div x-show="result" x-cloak class="mt-5 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Quotation Found</h3>
                <p class="text-xs text-gray-500 mt-0.5" x-text="result?.quotation.quotation_number"></p>
            </div>
            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-[#0066B3] to-[#263B8B] flex items-center justify-center text-white text-xs font-bold shrink-0"
                  x-text="(result?.quotation.company || '?').charAt(0)"></span>
        </div>

        <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-5 text-sm">
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Company</p>
                <p class="font-semibold text-gray-800 mt-0.5" x-text="result?.quotation.company"></p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Vehicle</p>
                <p class="font-semibold text-gray-800 mt-0.5" x-text="(result?.quotation.car_make || '') + ' ' + (result?.quotation.car_model || '')"></p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Registration</p>
                <p class="font-semibold text-gray-800 mt-0.5" x-text="result?.quotation.car_registration || 'N/A'"></p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Premium</p>
                <p class="font-bold text-[#0066B3] mt-0.5" x-text="'KES ' + Number(result?.quotation.total_premium || 0).toLocaleString('en-KE', { minimumFractionDigits: 2 })"></p>
            </div>
        </div>

        {{-- Sticker already issued --}}
        <template x-if="result && result.sticker">
            <div class="px-6 py-4 bg-emerald-50 border-t border-emerald-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-sm text-emerald-700 font-medium">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Sticker <strong x-text="result.sticker.sticker_number"></strong> already <span x-text="result.sticker.status"></span></span>
                </div>
                <a href="javascript:void(0)" @click="viewCertificate()" class="px-4 py-2 text-xs font-bold text-white bg-[#0066B3] hover:bg-[#263B8B] rounded-lg transition shrink-0">View Sticker →</a>
            </div>
        </template>

        {{-- No sticker yet: logged in and authorised → proceed directly --}}
        <template x-if="result && !result.sticker && result.can_generate_sticker">
            <div class="px-6 py-4 bg-blue-50 border-t border-blue-100 flex items-center justify-between gap-3">
                <p class="text-sm text-blue-700 font-medium">You're ready to request a sticker for this quotation.</p>
                <a :href="result.generate_url" class="px-4 py-2 text-xs font-bold text-white bg-[#0066B3] hover:bg-[#263B8B] rounded-lg transition shrink-0">Proceed to Request for Sticker →</a>
            </div>
        </template>

        {{-- No sticker yet: not logged in → prompt to log in --}}
        <template x-if="result && !result.sticker && result.requires_login">
            <div class="px-6 py-4 bg-amber-50 border-t border-amber-100 flex items-center justify-between gap-3">
                <p class="text-sm text-amber-700 font-medium">Log in to request a sticker for this quotation.</p>
                <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-white bg-[#0066B3] hover:bg-[#263B8B] rounded-lg transition shrink-0">Log In →</a>
            </div>
        </template>

        {{-- No sticker yet: logged in but not the owner --}}
        <template x-if="result && !result.sticker && !result.can_generate_sticker && !result.requires_login">
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <p class="text-sm text-gray-500">This quotation belongs to a different account. Please log in with the account it was created under to generate a sticker.</p>
            </div>
        </template>
    </div>

    <div class="text-center mt-8">
        <a href="{{ route('stickers.create') }}" class="text-[13px] font-semibold text-[#0066B3] hover:text-[#263B8B]">
            Don't have a quotation yet? Generate one &rarr;
        </a>
    </div>
</section>

<x-public-footer/>
<x-auth-modal/>
<x-toast/>
<script>
window.MIC_GUEST = @json(auth()->guest());

function openAuthModal(tab) {
    if (!sessionStorage.getItem('auth_redirect')) {
        sessionStorage.setItem('auth_redirect', window.location.href);
    }
    document.getElementById('authModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if (tab && window.Alpine) {
        var el = document.getElementById('authModal');
        if (el) { try { window.Alpine.$data(el).tab = tab; } catch(e) {} }
    }
}

function closeAuthModal() {
    document.getElementById('authModal').style.display = 'none';
    document.body.style.overflow = '';
}

function stickerLookup() {
    return {
        number: '',
        loading: false,
        result: null,
        error: null,
        search() {
            if (!this.number || this.number.length < 2) return;
            this.loading = true;
            this.error = null;
            this.result = null;
            fetch('{{ route('stickers.lookup.search-quotation') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ search: this.number })
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) { this.error = data.error || 'No quotation found matching your search.'; return; }
                this.result = data;
            })
            .catch(() => { this.error = 'Failed to look up. Please try again.'; })
            .finally(() => { this.loading = false; });
        },
        viewCertificate() {
            if (window.MIC_GUEST) {
                sessionStorage.setItem('auth_redirect', '{{ route('dashboard') }}');
                openAuthModal();
                return;
            }
            window.location.href = '{{ route('dashboard') }}';
        }
    };
}
</script>
</body>
</html>
