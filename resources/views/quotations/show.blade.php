<x-app-layout>
<div class="max-w-4xl mx-auto space-y-5 pt-10 fade-in">

    @if(!empty($showSuccess))
    <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-5 shadow-sm flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Your quotation was successfully generated</p>
            <p class="text-xs text-emerald-500/70 mt-0.5">Your quotation has been saved and is now available in your account.</p>
        </div>
    </div>
    @endif

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Quotation / {{ $quotation->company->name }}</p>
                <div class="flex items-center gap-3 mt-0.5">
                    <h1 class="text-2xl font-bold text-white tracking-tight" style="font-size:26px">{{ $quotation->quotation_number }}</h1>
                    <button onclick="copyToClipboard('{{ $quotation->quotation_number }}')" class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold bg-white/20 text-white rounded-lg hover:bg-white/30 transition" style="border:1px solid rgba(255,255,255,0.25)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
                <p class="mt-1 text-xs text-slate-400">
                    Generated {{ $quotation->created_at->format('d M Y, h:i A') }}
                    &middot; Status:
                    @php
                        $statusColors = [
                            'processing' => 'bg-blue-500/20 text-blue-300',
                            'completed' => 'bg-emerald-500/20 text-emerald-300',
                            'closed' => 'bg-gray-500/20 text-gray-300',
                            'insuring_sticker' => 'bg-amber-500/20 text-amber-300',
                            'active' => 'bg-emerald-500/20 text-emerald-300',
                            'draft' => 'bg-gray-500/20 text-gray-300',
                            'expired' => 'bg-red-500/20 text-red-300',
                            'generated' => 'bg-blue-500/20 text-blue-300',
                            'pending' => 'bg-yellow-500/20 text-yellow-300',
                        ];
                        $statusClass = $statusColors[$quotation->status] ?? 'bg-gray-500/20 text-gray-300';
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusClass }}">{{ ucfirst($quotation->status) }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('quotations.download.pdf', $quotation) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download PDF
                </a>
                <a href="{{ route('quotations.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-white/20 text-white/80 hover:bg-white/10 rounded-lg transition">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Vehicle summary bar --}}
        <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-white/70">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                {{ $quotation->car_registration ?? 'No reg.' }}
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                KES {{ number_format($quotation->car_valuation, 2) }}
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ $quotation->company->name }}
            </span>
        </div>
    </div>

    {{-- ─── PERSONAL DETAILS ────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Customer</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Personal Details</h2>
            </div>
            @if($quotation->user_id !== auth()->id())
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-semibold bg-amber-500/20 text-amber-600 dark:text-amber-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    On behalf of {{ $quotation->user->name ?? 'another user' }}
                </span>
            @endif
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6 text-xs">
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Name</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->user->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->user->email ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Phone</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->user->mobile ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Insurer</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->company->name }}</span>
            </div>
        </div>
    </div>

    {{-- ─── VEHICLE DETAILS ─────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6 text-xs">
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Make</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->car_make }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Model</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->car_model }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Year</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->car_year }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Registration</span>
                <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $quotation->car_registration ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- ─── PREMIUM BREAKDOWN ───────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Financials</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Breakdown</h2>
        </div>
        <div class="p-6">
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Vehicle Value</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($quotation->car_valuation, 2) }}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Basic Premium ({{ $quotation->breakdown['premium_rate'] ?? 6.5 }}%)</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($quotation->premium, 2) }}</span>
                </div>
                @php
                    $breakdown = $quotation->breakdown ?? [];
                    $basePremium = $breakdown['base_premium'] ?? null;
                    $iraLevy = $breakdown['ira_levy'] ?? null;
                @endphp
                @if($basePremium !== null && $iraLevy !== null)
                <div class="flex justify-between py-1.5 pl-4">
                    <span class="text-gray-400">Base Premium ({{ number_format(($breakdown['premium_rate'] ?? 6.5) - ($breakdown['ira_levy_rate'] ?? 0.5), 1) }}%)</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($basePremium, 2) }}</span>
                </div>
                <div class="flex justify-between py-1.5 pl-4">
                    <span class="text-gray-400">IRA Levy ({{ $breakdown['ira_levy_rate'] ?? 0.5 }}%)</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($iraLevy, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">PVT Premium</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($quotation->pvt_premium, 2) }}</span>
                </div>

                @if($quotation->breakdown)
                    @foreach($quotation->breakdown as $key => $value)
                        @if(!in_array($key, ['car_valuation', 'premium', 'pvt_premium', 'total_premium', 'base_premium', 'ira_levy', 'premium_rate', 'ira_levy_rate', 'basic_premium']))
                        <div class="flex justify-between py-1.5">
                            <span class="text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ is_numeric($value) ? 'KES ' . number_format($value, 2) : $value }}</span>
                        </div>
                        @endif
                    @endforeach
                @endif

                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Excess - Own Damage</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($quotation->excess_own_damage ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-gray-500">Excess - Theft</span>
                    <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($quotation->excess_theft ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between py-3 mt-2 border-t-2 border-gray-200 dark:border-gray-600">
                    <span class="font-bold text-gray-800 dark:text-white text-sm">Total Premium</span>
                    <span class="font-extrabold text-lg text-blue-600 dark:text-blue-400">KES {{ number_format($quotation->total_premium, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── STATUS UPDATE ────────────────────────────────────────────────── --}}
    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Workflow</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Update Status</h2>
        </div>
        <div class="p-6">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Current status: <strong>{{ ucfirst($quotation->status) }}</strong></p>
            <div class="flex flex-wrap gap-2">
                @php $allStatuses = ['processing', 'completed', 'closed', 'insuring_sticker', 'active', 'expired']; @endphp
                @foreach($allStatuses as $st)
                    <form action="{{ route('quotations.update-status', $quotation) }}" method="POST" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='...';" style="display:inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $st }}">
                        <button type="submit" {{ $quotation->status === $st ? 'disabled' : '' }}
                                style="padding:6px 14px;font-size:11px;font-weight:600;border-radius:8px;border:1px solid {{ $quotation->status === $st ? '#005F9E' : 'rgba(0,0,0,0.12)' }};background:{{ $quotation->status === $st ? '#005F9E' : 'transparent' }};color:{{ $quotation->status === $st ? '#fff' : 'inherit' }};cursor:{{ $quotation->status === $st ? 'default' : 'pointer' }};transition:all .15s;font-family:inherit"
                                onmouseover="this.style.borderColor='#005F9E';this.style.background='{{ $quotation->status === $st ? '#005F9E' : 'rgba(0,95,158,0.08)' }}';this.style.color='{{ $quotation->status === $st ? '#fff' : '#005F9E' }}'"
                                onmouseout="this.style.borderColor='{{ $quotation->status === $st ? '#005F9E' : 'rgba(0,0,0,0.12)' }}';this.style.background='{{ $quotation->status === $st ? '#005F9E' : 'transparent' }}';this.style.color='{{ $quotation->status === $st ? '#fff' : 'inherit' }}'">
                            {{ ucfirst(str_replace('_', ' ', $st)) }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ─── STICKER GENERATION ──────────────────────────────────────────── --}}
    @if(in_array($quotation->status, ['insuring_sticker', 'completed', 'active']) && (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Sticker</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Insurance Certificate / Sticker</h2>
        </div>
        <div class="p-6 text-center">
            <a href="{{ route('stickers.create', ['quotation_id' => $quotation->id]) }}"
               class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold bg-amber-500 hover:bg-amber-400 text-gray-900 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                Generate Certificate / Sticker
            </a>
        </div>
    </div>
    @endif

</div>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            var btn = event.currentTarget;
            var orig = btn.innerHTML;
            btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
            btn.classList.add('copied');
            setTimeout(function() { btn.innerHTML = orig; btn.classList.remove('copied'); }, 2000);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        alert('Copied!');
    }
}
</script>
</x-app-layout>