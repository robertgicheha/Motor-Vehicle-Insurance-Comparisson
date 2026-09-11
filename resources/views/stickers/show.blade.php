<x-app-layout>
<div class="max-w-5xl mx-auto space-y-6 pt-10 fade-in font-montserrat">

    {{-- ─── SUCCESS / ERROR ALERTS ────────────────────────────────────── --}}
    @if(session('success'))
    <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-5 shadow-sm flex items-center gap-4 print:hidden">
        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-2xl bg-red-500/10 border border-red-500/30 px-6 py-5 shadow-sm flex items-center gap-4 print:hidden">
        <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- ─── HERO ───────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm print:hidden">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Insurance Sticker</p>
        <h1 class="mt-0.5 text-2xl font-bold text-white tracking-tight">{{ $sticker->vehicle_registration ?: $sticker->customer_name }}</h1>
        <p class="mt-1 text-xs text-blue-200">{{ $sticker->company->name }} &mdash; {{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</p>
        @if($sticker->underwriter_sticker_no)
        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-4 py-1.5 text-xs font-bold text-white tracking-wide">
            <span class="opacity-70 font-medium">Sticker Number:</span>
            <span>{{ $sticker->underwriter_sticker_no }}</span>
        </div>
        @else
        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 px-4 py-1.5 text-xs font-semibold text-white/80 tracking-wide">
            Awaiting the underwriter's sticker number
        </div>
        @endif
        <p class="mt-2 text-[10px] text-blue-300/70">Internal reference: {{ $sticker->sticker_number }}</p>
    </div>

    {{-- ─── SUMMARY CARDS ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">

        {{-- Sticker Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Sticker</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Sticker Info</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
                @if($sticker->underwriter_sticker_no)
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Sticker Number</span>
                    <span class="font-bold text-gray-800 dark:text-white text-right">{{ $sticker->underwriter_sticker_no }}</span>
                </div>
                @endif
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Company</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->company->name }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Status</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                            'issued' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        ];
                        $colorClass = $statusColors[$sticker->status] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $colorClass }}">{{ ucfirst($sticker->status) }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Issued By</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->issuedBy->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Created Via</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ strtoupper($sticker->created_via ?? 'web') }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Generated</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->generated_at?->format('d M Y, h:i A') ?? 'Pending underwriter response' }}</span>
                </div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Customer</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Personal Details</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Name</span>
                    <span class="font-bold text-gray-800 dark:text-white text-right">{{ $sticker->customer_name }}</span>
                </div>
                @if($sticker->customer_email)
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Email</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right break-all">{{ $sticker->customer_email }}</span>
                </div>
                @endif
                @if($sticker->customer_phone)
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Phone</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->customer_phone }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Premium --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Premium</p>
                <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Details</h2>
            </div>
            <div class="p-5 space-y-3 text-xs">
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Valuation</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">KES {{ number_format($sticker->car_valuation, 2) }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Rate</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->premium_rate ? number_format($sticker->premium_rate, 2) . '%' : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-start gap-3">
                    <span class="text-gray-400 shrink-0">Cover Type</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right">{{ $sticker->cover_type }}</span>
                </div>
                <div class="flex justify-between items-start gap-3 border-t border-gray-100 dark:border-gray-700 pt-3">
                    <span class="font-bold text-gray-800 dark:text-white shrink-0">Premium</span>
                    <span class="font-extrabold text-lg text-blue-600 dark:text-blue-400 text-right">KES {{ number_format($sticker->premium, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── VEHICLE DETAILS ────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Registration No</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->vehicle_registration ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Chassis No</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->chassis_number ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Engine No</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->engine_number ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Make</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->car_make }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Model</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->car_model }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Year of Manufacture</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->car_year }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Color</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->color ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Valuation</span>
                <span class="font-semibold text-gray-800 dark:text-white">KES {{ number_format($sticker->car_valuation, 2) }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Cover Type</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->cover_type }}</span>
            </div>
        </div>
    </div>

    {{-- ─── EMAIL THIS CERTIFICATE ─────────────────────────────────────── --}}
    {{-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Share</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Email This Sticker</h2>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('public.sticker.email') }}" class="flex gap-3 items-end">
                @csrf
                <input type="hidden" name="number" value="{{ $sticker->sticker_number }}">
                <div class="flex-1">
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="Enter recipient email address"
                           class="w-full px-3 py-2.5 text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Send Email
                </button>
            </form>
        </div>
    </div> --}}

    {{-- ─── UNDERWRITER RESPONSE ───────────────────────────────────────── --}}
    @if($sticker->email_sent_at)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Underwriter</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Response Tracking</h2>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Email Sent</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->email_sent_at->format('d M Y, h:i A') }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Response Received</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->response_received_at?->format('d M Y, h:i A') ?? 'Awaiting response' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Turnaround</span>
                @php
                    $turnaround = $sticker->getTurnaroundStatus();
                    $turnaroundLabel = match($turnaround) {
                        'completed' => 'Completed',
                        'breached' => 'SLA Breached',
                        'pending_response' => 'Within SLA',
                        default => 'Pending',
                    };
                    $turnaroundColor = match($turnaround) {
                        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        'breached' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    };
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $turnaroundColor }}">{{ $turnaroundLabel }}</span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px] mb-1">Response Notes</span>
                <span class="font-semibold text-gray-800 dark:text-white">{{ $sticker->response_notes ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── PREMIUM BREAKDOWN ──────────────────────────────────────────── --}}
    @if($sticker->breakdown && count($sticker->breakdown) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print:hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Financials</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Premium Breakdown</h2>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px]">
                        <th class="text-left font-semibold pb-2">Description</th>
                        <th class="text-right font-semibold pb-2">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($sticker->breakdown as $key => $value)
                    <tr class="hover:bg-blue-50 dark:hover:bg-blue-900/20">
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                        <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ is_numeric($value) ? number_format($value, 2) : $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                        <td class="pt-3 font-bold text-gray-800 dark:text-white">Total Premium</td>
                        <td class="pt-3 text-right font-extrabold text-lg text-blue-600 dark:text-blue-400">KES {{ number_format($sticker->premium, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── STATUS: WAITING FOR UNDERWRITER ────────────────────────────── --}}
    @if($sticker->status === 'pending' || $sticker->status === 'issued')
    <div class="rounded-2xl bg-amber-500/10 border border-amber-500/30 px-6 py-5 shadow-sm flex items-center gap-4 print:hidden">
        <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-amber-500 animate-pulse" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-700 dark:text-amber-400">
                @if($sticker->status === 'pending')
                    Waiting to be issued &mdash; KYC documents uploaded, click Issue to send to underwriter.
                @else
                    Sticker request was successfull &mdash; awaiting underwriter response.
                @endif
            </p>
            @if($sticker->email_sent_at)
            <p class="text-xs text-amber-600/70 dark:text-amber-500/70 mt-0.5">Email sent {{ $sticker->email_sent_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>
    @endif

</div>
</x-app-layout>
