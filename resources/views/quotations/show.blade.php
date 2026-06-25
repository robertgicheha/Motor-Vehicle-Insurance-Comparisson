<x-app-layout>
<div class="max-w-4xl mx-auto space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Quotation / Details</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ $quotation->company->name }}</h1>
                <p class="mt-0.5 text-xs text-slate-400">{{ $quotation->quotation_number }}</p>
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
        </div>
    </div>

    {{-- Main Detail Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Premium Summary</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Total Premium</h2>
            </div>
            <div class="text-right">
                <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">KES {{ number_format($quotation->total_premium, 2) }}</p>
            </div>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-5">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Quotation #</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $quotation->quotation_number }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Car Registration</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $quotation->car_registration ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Car Valuation</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">KES {{ number_format($quotation->car_valuation, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Basic Premium</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">KES {{ number_format($quotation->premium, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">PVT Premium</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">KES {{ number_format($quotation->pvt_premium, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Status</p>
                @php
                    $statusColors = [
                        'processing' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'closed' => 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                        'insuring_sticker' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        'active' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'draft' => 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                        'expired' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        'generated' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                        'pending' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    ];
                    $statusClass = $statusColors[$quotation->status] ?? 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($quotation->status) }}</span>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Excess - Own Damage</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">KES {{ number_format($quotation->excess_own_damage ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Excess - Theft</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">KES {{ number_format($quotation->excess_theft ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Generated On</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $quotation->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Insurer</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $quotation->company->name }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Requested By</p>
                <p class="text-xs font-bold text-gray-900 dark:text-white mt-0.5">{{ $quotation->user->name ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Admin: Status Update Form --}}
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <form action="{{ route('quotations.update-status', $quotation) }}" method="POST" class="flex items-center gap-3">
                @csrf @method('PATCH')
                <label class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 shrink-0">Update Status</label>
                <select name="status" class="px-3 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="processing" {{ $quotation->status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $quotation->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="closed" {{ $quotation->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="insuring_sticker" {{ $quotation->status === 'insuring_sticker' ? 'selected' : '' }}>Insuring Sticker</option>
                    <option value="active" {{ $quotation->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ $quotation->status === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition">Apply</button>
            </form>
        </div>
        @endif
    </div>

    {{-- Premium Breakdown --}}
    @if($quotation->breakdown)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Breakdown</p>
                <h3 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Breakdown</h3>
            </div>
            <div class="p-6 space-y-2">
                @foreach($quotation->breakdown as $key => $value)
                    <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                        <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ is_numeric($value) ? 'KES ' . number_format($value, 2) : $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Sticker Section --}}
    @if($quotation->status === 'insuring_sticker' && auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Sticker</p>
            <h3 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Insurance Certificate / Sticker</h3>
        </div>
        <div class="p-6 text-center">
            <a href="{{ route('stickers.create', ['quotation_id' => $quotation->id]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-amber-500 hover:bg-amber-400 text-gray-900 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                Generate Certificate / Sticker
            </a>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
