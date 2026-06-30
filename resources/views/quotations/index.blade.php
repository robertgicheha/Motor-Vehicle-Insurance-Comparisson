<x-app-layout>
    @php
    $companiesJson = $companies->values()->map(fn($c, $idx) => [
        'slug'  => $c->slug,
        'name'  => $c->name,
        'rate'  => (optional($c->rates->firstWhere('value_type', 'percentage'))->percentage ?? 3.5) / 100,
        'color' => ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b'][$idx % 5],
    ]);
    @endphp

{{-- ─── MAIN PAGE ─────────────────────────────────────────────────────────── --}}
<div class="space-y-6 pt-10 fade-in" x-data="quotationsPage()" x-init="init()">

    {{-- ─── HERO ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">MIC Global Risks · Insurance Management</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Quotation Registry</h1>
                <p class="mt-0.5 text-xs text-slate-400">All motor insurance quotations across active underwriters</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                <button onclick="openApplyModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Apply for User
                </button>
                @endif
                <button onclick="openNewQuoteModal()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Quotation
                </button>
            </div>
        </div>

        {{-- KPI row --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Quotations</p>
                <p class="kpi-value mt-1">{{ $analytics['total'] }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All time</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Today</p>
                <p class="kpi-value mt-1">{{ $analytics['today'] }}</p>
                @if($analytics['today'] > 0)
                    <span class="kpi-delta up mt-1.5 inline-block">Active</span>
                @else
                    <span class="kpi-delta neutral mt-1.5 inline-block">No new</span>
                @endif
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">This Week</p>
                <p class="kpi-value mt-1">{{ $analytics['this_week'] }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">{{ $analytics['this_week'] }} new</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">This Month</p>
                <p class="kpi-value mt-1">{{ $analytics['this_month'] }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">{{ $analytics['this_month'] }} new</span>
            </div>
        </div>
    </div>

      {{-- ─── TABLE ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quotations</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $quotations->total() }} records</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                    </svg>
                    <input x-model="search" @input.debounce.300ms="filterTable()"
                        type="text" placeholder="Search quotations..."
                        class="pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none w-44">
                </div>
                <select x-model="statusFilter" @change="filterTable()"
                    class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All statuses</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="active">Active</option>
                    <option value="draft">Draft</option>
                    <option value="closed">Closed</option>
                    <option value="insuring_sticker">Insuring Sticker</option>
                    <option value="expired">Expired</option>
                </select>
                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">#</th>
                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                            <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">User</th>
                        @endif
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Underwriter</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Valuation</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($quotations as $q)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30"
                            x-show="rowVisible($el)"
                            data-search="{{ strtolower($q->quotation_number . ' ' . $q->car_make . ' ' . $q->car_model . ' ' . ($q->user->name ?? '') . ' ' . ($q->company->name ?? '')) }}"
                            data-status="{{ strtolower($q->status) }}">
                            <td class="px-5 py-2.5">
                                <a href="{{ route('quotations.show', $q) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $q->quotation_number }}</a>
                            </td>
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <td class="px-5 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                            {{ strtoupper(substr($q->user->name, 0, 2)) }}
                                        </div>
                                        <span class="font-medium text-gray-800 dark:text-white">{{ $q->user->name }}</span>
                                    </div>
                                </td>
                            @endif
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $q->company->name }}</td>
                            <td class="px-5 py-2.5">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $q->car_make }} {{ $q->car_model }}</span>
                                <p class="text-[10px] text-gray-400">{{ $q->car_year }}</p>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400 font-mono">{{ number_format($q->car_valuation, 0) }}</td>
                            <td class="px-5 py-2.5 text-right font-bold text-gray-800 dark:text-white font-mono">{{ number_format($q->total_premium, 0) }}</td>
                            <td class="px-5 py-2.5 text-center">
                                @php
                                    $statusColors = [
                                        'processing'      => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'completed'       => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'closed'          => 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                        'insuring_sticker'=> 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        'active'          => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'draft'           => 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                        'expired'         => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'generated'       => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'pending'         => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColors[$q->status] ?? 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst(str_replace('_', ' ', $q->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $q->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('quotations.show', $q) }}"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="View">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                    <button onclick="openApplyFor({{ $q->user_id }}, '{{ addslashes($q->user->name) }}')"
                                        class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition" title="Apply for this user">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </button>
                                    @endif
                                    <form action="{{ route('quotations.destroy', $q) }}" method="POST"
                                        onsubmit="return confirm('Permanently delete quotation {{ $q->quotation_number }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition" title="Delete">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin') ? 9 : 8 }}"
                                class="px-5 py-10 text-center text-xs text-gray-400">No quotations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $quotations->links() }}</div>
        @endif
    </div>



    {{-- ─── CHARTS ROW ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Analytics</p>
                <h3 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Quotations by Underwriter</h3>
            </div>
            <div class="p-5">
                <canvas id="companyChart" height="200"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Premium</p>
                <h3 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Distribution by Insurer</h3>
            </div>
            <div class="p-5">
                <canvas id="premiumChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- ─── TREND CHART ────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Trend</p>
            <h3 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Daily Quotations — Last 14 Days</h3>
        </div>
        <div class="p-5">
            <canvas id="trendChart" height="120"></canvas>
        </div>
    </div>

  

</div>
{{-- ─── END MAIN PAGE (x-data scope closed) ──────────────────────────────── --}}


{{-- ─── NEW QUOTATION MODAL (outside Alpine scope) ───────────────────────── --}}
<div id="newQuoteModal" class="fixed inset-0 z-50 hidden" x-data="quotationsPage()">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeNewQuoteModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">New Quotation</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Motor Insurance Quote</h2>
            </div>
            <button onclick="closeNewQuoteModal()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">

            {{-- Step indicators --}}
            <div class="flex gap-2 mb-6">
                <template x-for="(label, i) in ['Personal Details', 'Vehicle Details', 'Quote Generation']" :key="i">
                    <div @click="quoteStep = i + 1"
                        class="flex-1 px-3 py-2.5 rounded-lg border text-center cursor-pointer transition"
                        :class="{
                            'border-blue-500 bg-blue-50 dark:bg-blue-900/20': quoteStep === i+1,
                            'border-emerald-300 bg-emerald-50 dark:bg-emerald-900/20': quoteStep > i+1,
                            'border-gray-200 dark:border-gray-700': quoteStep < i+1
                        }">
                        <div class="text-[9px] font-bold uppercase tracking-wider"
                            :class="{
                                'text-blue-600 dark:text-blue-400': quoteStep === i+1,
                                'text-emerald-600 dark:text-emerald-400': quoteStep > i+1,
                                'text-gray-400': quoteStep < i+1
                            }">Step <span x-text="i+1"></span></div>
                        <div class="text-xs font-semibold mt-0.5"
                            :class="{
                                'text-blue-700 dark:text-blue-300': quoteStep === i+1,
                                'text-emerald-700 dark:text-emerald-300': quoteStep > i+1,
                                'text-gray-500 dark:text-gray-400': quoteStep < i+1
                            }" x-text="label"></div>
                    </div>
                </template>
            </div>

            {{-- Step 1: Personal Details --}}
            <div x-show="quoteStep === 1" class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white">Personal Details</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Please fill in the contact details of the insured party.</p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Salutation <span class="text-rose-500">*</span></label>
                        <select x-model="qf.salutation" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select...</option>
                            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input x-model="qf.first_name" type="text" placeholder="e.g. John" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Middle Name</label>
                        <input x-model="qf.middle_name" type="text" placeholder="Optional" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Last Name <span class="text-rose-500">*</span></label>
                        <input x-model="qf.last_name" type="text" placeholder="e.g. Doe" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Phone Number <span class="text-rose-500">*</span></label>
                        <input x-model="qf.phone" type="tel" placeholder="07XX XXX XXX" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email Address <span class="text-rose-500">*</span></label>
                        <input x-model="qf.email" type="email" placeholder="email@example.com" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button onclick="closeNewQuoteModal()" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Cancel</button>
                    <button @click="quoteStep = 2" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Continue to Vehicle Details →</button>
                </div>
            </div>

            {{-- Step 2: Vehicle Details --}}
            <div x-show="quoteStep === 2" class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white">Vehicle Details</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Provide accurate vehicle information for a precise premium calculation.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Registration Number <span class="text-rose-500">*</span></label>
                        <input x-model="qf.reg" type="text" placeholder="e.g. KDH 724V" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Chassis / Frame Number</label>
                        <input x-model="qf.chassis" type="text" placeholder="Optional" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Make of Vehicle <span class="text-rose-500">*</span></label>
                        <select x-model="qf.make" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select make...</option>
                            <option>Toyota</option><option>Honda</option><option>Volkswagen</option>
                            <option>Subaru</option><option>Nissan</option><option>Mitsubishi</option>
                            <option>Mazda</option><option>Mercedes-Benz</option><option>BMW</option>
                            <option>Ford</option><option>Other</option>
                        </select>
                        <input x-show="qf.make === 'Other'" x-model="qf.make_other" type="text" placeholder="Type vehicle make…" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none mt-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Model Name <span class="text-rose-500">*</span></label>
                        <input x-model="qf.model" type="text" placeholder="e.g. Vezel, Mark X" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Body Type</label>
                        <select x-model="qf.body" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select body type...</option>
                            <option>Saloon</option><option>Station Wagon</option><option>SUV/4x4</option>
                            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option>
                            <option>Convertible</option><option>Coupe</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Year of Manufacture <span class="text-rose-500">*</span></label>
                        <input x-model="qf.year" type="number" placeholder="e.g. 2020" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Color</label>
                        <select x-model="qf.color" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select color...</option>
                            <option>White</option><option>Black</option><option>Silver/Grey</option>
                            <option>Blue</option><option>Red</option><option>Brown/Maroon</option>
                            <option>Gold/Champagne</option><option>Green</option><option>Other</option>
                        </select>
                        <input x-show="qf.color === 'Other'" x-model="qf.color_other" type="text" placeholder="Type color…" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none mt-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value of Vehicle (KES) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">KES</span>
                            <input x-model="qf.value" @input="calcPremiums()" type="number" placeholder="0" class="w-full pl-11 pr-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between pt-2">
                    <button @click="quoteStep = 1" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Back</button>
                    <button @click="quoteStep = 3" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Continue to Quote Generation →</button>
                </div>
            </div>

            {{-- Step 3: Quote Generation --}}
            <div x-show="quoteStep === 3" class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white">Premium Estimate</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Select an insurer card to open its full quotation, or compare all side by side.</p>
                </div>

                <div x-show="!qf.value" class="text-center py-8 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-400">
                    Go back to Step 2 and enter the <strong>vehicle value</strong> to see premium estimates.
                </div>

                <div x-show="qf.value" class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <template x-for="company in companies" :key="company.slug">
                            <div @click="proceedToCreate(company.slug)"
                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition flex flex-col items-center gap-1.5">
                                <div class="text-[10px] font-bold uppercase tracking-wider" x-text="company.name" :style="'color:' + company.color"></div>
                                <div class="text-[10px] text-gray-400" x-text="'@ ' + (company.rate * 100) + '%'"></div>
                                <div class="text-lg font-extrabold" x-text="'KES ' + (getPremium(company.slug) ? getPremium(company.slug).toLocaleString() : '—')" :style="'color:' + company.color"></div>
                                <div class="w-full text-[10px] font-bold text-white rounded-md py-1.5 mt-1" x-text="'View Quote →'" :style="'background:' + company.color"></div>
                            </div>
                        </template>
                        <div @click="proceedToCreate('ALL')"
                            class="bg-white dark:bg-gray-800 border-2 border-red-300 dark:border-red-700 rounded-xl p-4 text-center cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition flex flex-col items-center gap-1.5">
                            <div class="text-[10px] font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">Compare All</div>
                            <div class="text-[10px] text-gray-400">All insurers</div>
                            <div class="text-lg font-extrabold text-red-700 dark:text-red-400" x-text="companies.length + ' Quotes'"></div>
                            <div class="w-full text-[10px] font-bold text-white bg-red-700 rounded-md py-1.5 mt-1">Compare →</div>
                        </div>
                    </div>

                    {{-- Summary bar --}}
                    <div class="flex items-center gap-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-5 py-3">
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Vehicle Value</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="'KES ' + (qf.value ? parseFloat(qf.value).toLocaleString() : '0')"></span>
                        </div>
                        <div class="w-px h-8 bg-blue-200 dark:bg-blue-700"></div>
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Lowest</span>
                            <span class="text-sm font-bold text-emerald-600" x-text="'KES ' + (lowestPremium ? lowestPremium.toLocaleString() : '0')"></span>
                        </div>
                        <div class="w-px h-8 bg-blue-200 dark:bg-blue-700"></div>
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Highest</span>
                            <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="'KES ' + (highestPremium ? highestPremium.toLocaleString() : '0')"></span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-start pt-2">
                    <button @click="quoteStep = 2" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Back to Vehicle Details</button>
                </div>
            </div>

        </div>
        {{-- /Body --}}

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 shrink-0 text-right">
            <button @click="resetQuoteForm()" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Reset</button>
        </div>

    </div>
</div>
{{-- ─── END NEW QUOTATION MODAL ────────────────────────────────────────────── --}}


{{-- ─── ADMIN: APPLY FOR USER MODAL (outside Alpine scope) ───────────────── --}}
@if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
<div id="applyModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeApplyModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-900 shadow-2xl flex flex-col">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Admin Action</p>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">Apply Quotation for User</h2>
            </div>
            <button onclick="closeApplyModal()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5">
            <form id="applyForm" action="{{ route('quotations.create') }}" method="GET" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Select User</label>
                    <select id="applyUserSelect" name="on_behalf_of" required
                        class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">— Choose a user —</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="applyUserBanner" class="hidden bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2.5 text-xs text-blue-700 dark:text-blue-300 flex gap-2 items-center">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Creating on behalf of <strong id="applyUserNameLabel" class="ml-1"></strong>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2.5 text-[10px] text-amber-800 dark:text-amber-300">
                    The quotation will be saved under the selected user's account.
                </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2 shrink-0">
            <button onclick="closeApplyModal()" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Cancel</button>
            <button onclick="document.getElementById('applyForm').submit()" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Create Quotation</button>
        </div>

    </div>
</div>
@endif
{{-- ─── END APPLY MODAL ────────────────────────────────────────────────────── --}}


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
function quotationsPage() {
    return {
        search: '',
        statusFilter: '',
        quoteStep: 1,
        qf: {
            salutation: '', first_name: '', middle_name: '', last_name: '',
            phone: '', email: '', reg: '', chassis: '', make: '', make_other: '',
            model: '', body: '', year: '', color: '', color_other: '', value: ''
        },
        premiums: {},
        trendData:   @json($analytics['trend'] ?? []),
        companyData: @json($analytics['by_company'] ?? []),

        init() {
            this.$nextTick(() => {
                this.buildCompanyChart();
                this.buildPremiumChart();
                this.buildTrendChart();
            });
        },

        calcPremiums() {
            const v = parseFloat(this.qf.value) || 0;
            const tax = 0.005;
            this.premiums = {};
            this.companies.forEach(c => {
                this.premiums[c.slug] = Math.round(v * (c.rate + tax));
            });
        },

        getPremium(slug) {
            return this.premiums[slug] || 0;
        },

        get lowestPremium() {
            const vals = Object.values(this.premiums).filter(v => v > 0);
            return vals.length ? Math.min(...vals) : 0;
        },

        get highestPremium() {
            const vals = Object.values(this.premiums).filter(v => v > 0);
            return vals.length ? Math.max(...vals) : 0;
        },

        proceedToCreate(insurer) {
            const p = new URLSearchParams();
            Object.entries(this.qf).forEach(([k, v]) => { if (v) p.set(k, v); });
            p.set('insurer', insurer);
            sessionStorage.setItem('micQuoteData', JSON.stringify(this.qf));
            window.location.href = '{{ route("quotations.preview") }}?' + p.toString();
        },

        resetQuoteForm() {
            this.quoteStep = 1;
            this.qf = {
                salutation: '', first_name: '', middle_name: '', last_name: '',
                phone: '', email: '', reg: '', chassis: '', make: '', make_other: '',
                model: '', body: '', year: '', color: '', color_other: '', value: ''
            };
            this.premiums = {};
        },

        rowVisible(el) {
            const searchStr = (el.dataset.search || '').toLowerCase();
            const statusStr = (el.dataset.status || '').toLowerCase();
            return (!this.search || searchStr.includes(this.search.toLowerCase()))
                && (!this.statusFilter || statusStr === this.statusFilter.toLowerCase());
        },

        filterTable() {},

        buildCompanyChart() {
            const ctx = document.getElementById('companyChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.companyData.map(d => d.company),
                    datasets: [{
                        label: 'Quotations',
                        data: this.companyData.map(d => d.count),
                        backgroundColor: ['#1E40AF','#F59E0B','#10B981','#8B5CF6','#EF4444'],
                        borderRadius: 5,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: '#9ca3af' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { ticks: { font: { size: 10, weight: '600' }, color: '#6b7280' }, grid: { display: false } }
                    }
                }
            });
        },

        buildPremiumChart() {
            const ctx = document.getElementById('premiumChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: this.companyData.map(d => d.company),
                    datasets: [{
                        data: this.companyData.map(d => d.total_premium || 0),
                        backgroundColor: ['#1E40AF','#F59E0B','#10B981','#8B5CF6','#EF4444'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '68%',
                    plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, color: '#6b7280', boxWidth: 8, padding: 10 } } }
                }
            });
        },

        buildTrendChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.trendData.map(d => d.date),
                    datasets: [{
                        label: 'Quotations',
                        data: this.trendData.map(d => d.count),
                        borderColor: '#1E40AF',
                        backgroundColor: 'rgba(30,64,175,0.07)',
                        borderWidth: 2,
                        pointBackgroundColor: '#1E40AF',
                        pointRadius: 2.5,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: '#9ca3af' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { ticks: { font: { size: 9 }, color: '#9ca3af', maxRotation: 45 }, grid: { display: false } }
                    }
                }
            });
        }
    }
}

// ── Modal helpers ──────────────────────────────────────────────────────────────
function openNewQuoteModal() {
    document.getElementById('newQuoteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNewQuoteModal() {
    document.getElementById('newQuoteModal').classList.add('hidden');
    document.body.style.overflow = '';
    // Reset the modal's own Alpine instance
    const modal = document.getElementById('newQuoteModal');
    if (modal && modal._x_dataStack) {
        const data = Alpine.$data(modal);
        if (data && data.resetQuoteForm) data.resetQuoteForm();
    }
}

function openApplyModal() {
    document.getElementById('applyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function openApplyFor(userId, userName) {
    const modal = document.getElementById('applyModal');
    if (!modal) return;
    const select = document.getElementById('applyUserSelect');
    if (select) select.value = userId;
    const banner = document.getElementById('applyUserBanner');
    const label  = document.getElementById('applyUserNameLabel');
    if (banner) banner.classList.remove('hidden');
    if (label)  label.textContent = userName;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeApplyModal() {
    document.getElementById('applyModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeNewQuoteModal(); closeApplyModal(); }
});
</script>
@endpush

</x-app-layout>