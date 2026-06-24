<x-app-layout>

<div class="quotations-page" x-data="quotationsPage()" x-init="init()">

    {{-- ══════════════════════════════════════════ --}}
    {{-- HERO BAND                                  --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="hero-band px-6 pt-16 mt-10 pb-12 md:px-10">
        <div class="relative mx-auto">

            {{-- Top row --}}
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-8">
                <div>
                    <p style="font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin-bottom:8px;">
                        MIC Global Risks · Insurance Management
                    </p>
                    <h1 style="font-size:18px;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.3px;">
                        Quotation Registry
                    </h1>
                    <p style="color:rgba(255,255,255,0.55);margin-top:5px;font-size:11px;max-width:380px;line-height:1.5;">
                        All motor insurance quotations across active underwriters — GA Insurance and Sanlam Allianz.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 md:mt-1">
                    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                    <button @click="applyModal = true"
                        style="display:inline-flex;align-items:center;gap:6px;background:#F59E0B;color:#1a1a1a;font-size:11px;font-weight:700;padding:7px 14px;border-radius:7px;border:none;cursor:pointer;white-space:nowrap;transition:background .15s;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Apply for User
                    </button>
                    @endif
                    <button @click="newQuoteModal = true"
                        style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.1);color:#fff;font-size:11px;font-weight:600;padding:7px 14px;border-radius:7px;border:1px solid rgba(255,255,255,0.2);cursor:pointer;white-space:nowrap;transition:background .15s;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Quotation
                    </button>
                </div>
            </div>

            {{-- KPI row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
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
                        <span class="kpi-delta up mt-1.5 inline-block">↑ Active</span>
                    @else
                        <span class="kpi-delta neutral mt-1.5 inline-block">No new</span>
                    @endif
                </div>
                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                    <p class="kpi-eyebrow">This Week</p>
                    <p class="kpi-value mt-1">{{ $analytics['this_week'] }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">↑ {{ $analytics['this_week'] }} new</span>
                </div>
                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                    <p class="kpi-eyebrow">This Month</p>
                    <p class="kpi-value mt-1">{{ $analytics['this_month'] }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">↑ {{ $analytics['this_month'] }} new</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- ANALYTICS CARDS                            --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="mx-auto px-6 md:px-10 floating-cards-row">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <div class="analytics-card p-4 md:col-span-2">
                <p class="section-eyebrow mb-1">Analytics</p>
                <h3 style="font-size:12px;font-weight:700;color:#111827;margin-bottom:12px;">Quotations by Underwriter</h3>
                <div class="chart-wrap">
                    <canvas id="companyChart"></canvas>
                </div>
            </div>

            <div class="analytics-card p-4">
                <p class="section-eyebrow mb-1">Premium</p>
                <h3 style="font-size:12px;font-weight:700;color:#111827;margin-bottom:12px;">Distribution by Insurer</h3>
                <div class="chart-wrap">
                    <canvas id="premiumChart"></canvas>
                </div>
            </div>
        </div>

        <div class="analytics-card p-4 mt-3">
            <p class="section-eyebrow mb-1">Trend</p>
            <h3 style="font-size:12px;font-weight:700;color:#111827;margin-bottom:10px;">Daily Quotations — Last 14 Days</h3>
            <div style="height:140px;position:relative;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- TABLE SECTION                              --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="mx-auto px-6 md:px-10 mt-4 pb-12">
        <div class="analytics-card overflow-hidden">

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <p class="section-eyebrow">Quotations</p>
                    <span style="font-size:10px;color:#9ca3af;font-weight:500;">{{ $quotations->total() }} records</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                        </svg>
                        <input x-model="search" @input.debounce.300ms="filterTable()"
                            type="text" placeholder="Search quotations…" class="toolbar-search"/>
                    </div>
                    <select x-model="statusFilter" @change="filterTable()"
                        style="border:1px solid #e5e7eb;border-radius:7px;padding:6px 10px;font-size:11px;font-weight:600;color:#374151;background:#fff;cursor:pointer;">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="expired">Expired</option>
                    </select>
                    <button class="btn-secondary" style="font-size:11px;padding:6px 12px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">#</th>
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <th class="text-left">User</th>
                            @endif
                            <th class="text-left">Underwriter</th>
                            <th class="text-left">Vehicle</th>
                            <th class="text-right">Valuation (KES)</th>
                            <th class="text-right">Premium (KES)</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Date</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $q)
                            <tr x-show="rowVisible($el)"
                                data-search="{{ strtolower($q->quotation_number . ' ' . $q->car_make . ' ' . $q->car_model . ' ' . ($q->user->name ?? '') . ' ' . ($q->company->name ?? '')) }}"
                                data-status="{{ strtolower($q->status) }}">
                                <td>
                                    <a href="{{ route('quotations.show', $q) }}" class="qnum-link">{{ $q->quotation_number }}</a>
                                </td>
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                    <td>
                                        <div class="flex items-center gap-1.5">
                                            <div style="width:22px;height:22px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">
                                                {{ strtoupper(substr($q->user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-size:11px;color:#374151;">{{ $q->user->name }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td style="font-size:11px;font-weight:600;color:#1f2937;">{{ $q->company->name }}</td>
                                <td>
                                    <p style="font-size:11px;font-weight:600;color:#1f2937;margin:0;">{{ $q->car_make }} {{ $q->car_model }}</p>
                                    <p style="font-size:10px;color:#9ca3af;margin:0;">{{ $q->car_year }}</p>
                                </td>
                                <td class="text-right" style="font-size:11px;font-family:monospace;color:#6b7280;">{{ number_format($q->car_valuation, 0) }}</td>
                                <td class="text-right" style="font-size:11px;font-family:monospace;font-weight:700;color:#111827;">{{ number_format($q->total_premium, 0) }}</td>
                                <td class="text-center">
                                    @php
                                        $s = strtolower($q->status);
                                        $cls = match($s) { 'active' => 'status-active', 'draft' => 'status-draft', 'expired' => 'status-expired', default => 'status-active' };
                                    @endphp
                                    <span class="status-pill {{ $cls }}">{{ ucfirst($q->status) }}</span>
                                </td>
                                <td class="text-right" style="font-size:10px;color:#6b7280;">
                                    <div>{{ $q->created_at->format('d M Y') }}</div>
                                    <div style="color:#9ca3af;">{{ $q->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('quotations.show', $q) }}"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                        <button @click="openApplyFor({{ $q->user_id }}, '{{ addslashes($q->user->name) }}')"
                                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Apply for this user">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </button>
                                        @endif
                                        <form action="{{ route('quotations.destroy', $q) }}" method="POST"
                                            onsubmit="return confirm('Permanently delete quotation {{ $q->quotation_number }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding:48px;text-align:center;">
                                    <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                                        <div style="width:44px;height:44px;background:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p style="font-size:12px;color:#6b7280;font-weight:500;margin:0;">No quotations yet</p>
                                        <button @click="newQuoteModal = true" class="btn-primary" style="font-size:11px;padding:6px 14px;">Create first quotation</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quotations->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <p style="font-size:11px;color:#6b7280;">
                        Showing {{ $quotations->firstItem() }}–{{ $quotations->lastItem() }} of {{ $quotations->total() }}
                    </p>
                    {{ $quotations->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- NEW QUOTATION MODAL (inline multi-step)    --}}
    {{-- ══════════════════════════════════════════ --}}
    <div x-show="newQuoteModal" x-transition.opacity class="modal-overlay" style="display:none;align-items:flex-start;padding-top:40px;">
        <div class="modal-box" style="max-width:680px;max-height:calc(100vh - 80px);overflow-y:auto;" @click.stop>

            {{-- Modal header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div>
                    <p class="section-eyebrow" style="margin-bottom:3px;">New Quotation</p>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">Motor Insurance Quote</h3>
                </div>
                <button @click="newQuoteModal = false; resetQuoteForm()"
                    style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Step indicators --}}
            <div style="display:flex;gap:0;margin-bottom:20px;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                <template x-for="(label, i) in ['Personal Details', 'Vehicle Details', 'Quote Generation']" :key="i">
                    <div @click="quoteStep = i + 1"
                        :style="`flex:1;padding:8px 10px;text-align:center;cursor:pointer;transition:all .2s;
                            background: quoteStep === i+1 ? '#1E40AF' : (quoteStep > i+1 ? '#dbeafe' : '#f9fafb');
                            border-right: i < 2 ? '1px solid #e5e7eb' : 'none';`">
                        <div :style="`font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
                            color: quoteStep === i+1 ? 'rgba(255,255,255,0.7)' : (quoteStep > i+1 ? '#1d4ed8' : '#9ca3af');`">
                            Step <span x-text="i+1"></span>
                        </div>
                        <div :style="`font-size:11px;font-weight:600;margin-top:1px;
                            color: quoteStep === i+1 ? '#fff' : (quoteStep > i+1 ? '#1d4ed8' : '#374151');`"
                            x-text="label"></div>
                    </div>
                </template>
            </div>

            {{-- Step 1: Personal Details --}}
            <div x-show="quoteStep === 1">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                    <div class="mf-group">
                        <label class="mf-label">Salutation <span style="color:#ef4444">*</span></label>
                        <select x-model="qf.salutation" class="mf-input">
                            <option value="">Select...</option>
                            <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
                        </select>
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">First Name <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.first_name" type="text" placeholder="e.g. John" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Middle Name</label>
                        <input x-model="qf.middle_name" type="text" placeholder="Optional" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Last Name <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.last_name" type="text" placeholder="e.g. Doe" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Phone Number <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.phone" type="tel" placeholder="07XX XXX XXX" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Email Address <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.email" type="email" placeholder="email@example.com" class="mf-input">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                    <button @click="quoteStep = 2" class="btn-primary" style="font-size:11px;padding:7px 16px;">
                        Continue to Vehicle Details →
                    </button>
                </div>
            </div>

            {{-- Step 2: Vehicle Details --}}
            <div x-show="quoteStep === 2">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div class="mf-group">
                        <label class="mf-label">Registration Number <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.reg" type="text" placeholder="e.g. KDH 724V" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Chassis / Frame Number</label>
                        <input x-model="qf.chassis" type="text" placeholder="Optional" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Make of Vehicle <span style="color:#ef4444">*</span></label>
                        <select x-model="qf.make" class="mf-input">
                            <option value="">Select make...</option>
                            <option>Toyota</option><option>Honda</option><option>Volkswagen</option>
                            <option>Subaru</option><option>Nissan</option><option>Mitsubishi</option>
                            <option>Mazda</option><option>Mercedes-Benz</option><option>BMW</option>
                            <option>Ford</option><option>Other</option>
                        </select>
                        <input x-show="qf.make === 'Other'" x-model="qf.make_other" type="text" placeholder="Type vehicle make…" class="mf-input" style="margin-top:6px;">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Model Name <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.model" type="text" placeholder="e.g. Vezel, Mark X" class="mf-input">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Body Type</label>
                        <select x-model="qf.body" class="mf-input">
                            <option value="">Select body type...</option>
                            <option>Saloon</option><option>Station Wagon</option><option>SUV/4x4</option>
                            <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option>
                            <option>Convertible</option><option>Coupe</option>
                        </select>
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Year of Manufacture <span style="color:#ef4444">*</span></label>
                        <select x-model="qf.year" class="mf-input">
                            <option value="">Select year...</option>
                            <template x-for="y in yearRange" :key="y">
                                <option :value="y" x-text="y"></option>
                            </template>
                        </select>
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Color</label>
                        <select x-model="qf.color" class="mf-input">
                            <option value="">Select color...</option>
                            <option>White</option><option>Black</option><option>Silver/Grey</option>
                            <option>Blue</option><option>Red</option><option>Brown/Maroon</option>
                            <option>Gold/Champagne</option><option>Green</option><option>Other</option>
                        </select>
                        <input x-show="qf.color === 'Other'" x-model="qf.color_other" type="text" placeholder="Type color…" class="mf-input" style="margin-top:6px;">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Value of Vehicle (KES) <span style="color:#ef4444">*</span></label>
                        <input x-model="qf.value" @input="calcPremiums()" type="number" placeholder="e.g. 1500000" class="mf-input">
                    </div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:16px;">
                    <button @click="quoteStep = 1" class="btn-secondary" style="font-size:11px;padding:7px 14px;">← Back</button>
                    <button @click="quoteStep = 3" class="btn-primary" style="font-size:11px;padding:7px 16px;">Continue to Quotes →</button>
                </div>
            </div>

            {{-- Step 3: Quote Generation --}}
            <div x-show="quoteStep === 3">
                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:14px;">
                    <p style="font-size:11px;font-weight:700;color:#374151;margin:0 0 4px;">Premium Estimate</p>
                    <p style="font-size:10px;color:#9ca3af;margin:0 0 14px;">Click a card to proceed to the full quotation form.</p>

                    <div x-show="!qf.value" style="text-align:center;padding:16px;color:#9ca3af;font-size:11px;border:1.5px dashed #e5e7eb;border-radius:8px;">
                        ← Go back to Step 2 and enter the vehicle value to see estimates.
                    </div>

                    <div x-show="qf.value" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">

                        {{-- GA Insurance --}}
                        <div @click="proceedToCreate('GA')" style="background:#fff;border:1.5px solid #e5e7eb;border-radius:9px;padding:14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s;"
                            onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                            <p style="font-size:10px;font-weight:700;color:#1E40AF;margin:0 0 4px;">GA INSURANCE</p>
                            <p style="font-size:18px;font-weight:800;color:#1E40AF;margin:0;line-height:1.1;" x-text="premiums.ga ? 'KES ' + premiums.ga.toLocaleString() : '—'"></p>
                            <p style="font-size:10px;color:#9ca3af;margin:2px 0 10px;">@ 3.5%</p>
                            <div style="font-size:10px;font-weight:700;color:#fff;background:#1E40AF;border-radius:5px;padding:4px 0;">View Quote ↗</div>
                        </div>

                        {{-- Sanlam Allianz --}}
                        <div @click="proceedToCreate('SANLAM')" style="background:#fff;border:1.5px solid #e5e7eb;border-radius:9px;padding:14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s;"
                            onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                            <p style="font-size:10px;font-weight:700;color:#065f46;margin:0 0 4px;">SANLAM ALLIANZ</p>
                            <p style="font-size:18px;font-weight:800;color:#065f46;margin:0;line-height:1.1;" x-text="premiums.sanlam ? 'KES ' + premiums.sanlam.toLocaleString() : '—'"></p>
                            <p style="font-size:10px;color:#9ca3af;margin:2px 0 10px;">@ 4.5%</p>
                            <div style="font-size:10px;font-weight:700;color:#fff;background:#065f46;border-radius:5px;padding:4px 0;">View Quote ↗</div>
                        </div>

                        {{-- Compare All --}}
                        <div @click="proceedToCreate('ALL')" style="background:#fff;border:1.5px solid #fca5a5;border-radius:9px;padding:14px;text-align:center;cursor:pointer;transition:box-shadow .2s,transform .15s;grid-column:span 2;"
                            onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                            <p style="font-size:10px;font-weight:700;color:#c0392b;margin:0 0 2px;">COMPARE ALL INSURERS</p>
                            <p style="font-size:10px;color:#9ca3af;margin:0 0 8px;">Side-by-side comparison of all available quotes</p>
                            <div style="font-size:10px;font-weight:700;color:#fff;background:#c0392b;border-radius:5px;padding:4px 0;">Compare All ↗</div>
                        </div>
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-start;">
                    <button @click="quoteStep = 2" class="btn-secondary" style="font-size:11px;padding:7px 14px;">← Back to Vehicle Details</button>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- ADMIN: APPLY FOR USER MODAL               --}}
    {{-- ══════════════════════════════════════════ --}}
    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
    <div x-show="applyModal" x-transition.opacity class="modal-overlay" style="display:none">
        <div class="modal-box" @click.stop>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                <div>
                    <p class="section-eyebrow" style="margin-bottom:3px;">Admin Action</p>
                    <h3 style="font-size:14px;font-weight:700;color:#111827;margin:0;">Apply Quotation for User</h3>
                </div>
                <button @click="applyModal = false"
                    style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('quotations.create') }}" method="GET">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:10px;font-weight:700;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:.06em;">Select User</label>
                    <select name="on_behalf_of" x-model="selectedUser" required
                        style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 10px;font-size:12px;background:#fff;color:#111827;outline:none;">
                        <option value="">— Choose a user —</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="applyUserName" style="background:#eff6ff;border-radius:8px;padding:10px 12px;font-size:11px;color:#1d4ed8;margin-bottom:12px;display:flex;gap:6px;align-items:center;">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Creating on behalf of <strong x-text="applyUserName" style="margin-left:3px;"></strong>
                </div>
                <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:10px 12px;font-size:10px;color:#92400e;margin-bottom:16px;">
                    The quotation will be saved under the selected user's account.
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" @click="applyModal = false" class="btn-secondary" style="flex:1;justify-content:center;font-size:11px;padding:7px;">Cancel</button>
                    <button type="submit" class="btn-primary" style="flex:1;justify-content:center;font-size:11px;padding:7px;" :disabled="!selectedUser">
                        Create Quotation
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
.mf-group { display:flex; flex-direction:column; }
.mf-label { font-size:10px; font-weight:600; color:#374151; margin-bottom:4px; text-transform:uppercase; letter-spacing:.06em; }
.mf-input {
    border:1px solid #e5e7eb; border-radius:7px; padding:7px 10px;
    font-size:12px; color:#111827; background:#fff; outline:none;
    transition:border-color .15s, box-shadow .15s; width:100%; box-sizing:border-box;
    font-family:inherit;
}
.mf-input:focus { border-color:#1E40AF; box-shadow:0 0 0 3px rgba(30,64,175,.1); }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
function quotationsPage() {
    return {
        search: '',
        statusFilter: '',
        applyModal: false,
        selectedUser: '',
        applyUserName: '',
        newQuoteModal: false,
        quoteStep: 1,
        qf: {
            salutation:'', first_name:'', middle_name:'', last_name:'',
            phone:'', email:'', reg:'', chassis:'', make:'', make_other:'',
            model:'', body:'', year:'', color:'', color_other:'', value:''
        },
        premiums: { ga: 0, sanlam: 0 },
        yearRange: [],
        companyData: @json($analytics['by_company'] ?? []),
        trendData:   @json($analytics['trend'] ?? []),

        init() {
            const cur = new Date().getFullYear();
            this.yearRange = Array.from({length: cur - 1979}, (_, i) => cur - i);
            this.$nextTick(() => {
                this.buildCompanyChart();
                this.buildPremiumChart();
                this.buildTrendChart();
            });
        },

        calcPremiums() {
            const v = parseFloat(this.qf.value) || 0;
            this.premiums.ga     = Math.round(v * 0.035);
            this.premiums.sanlam = Math.round(v * 0.045);
        },

        proceedToCreate(insurer) {
            const p = new URLSearchParams();
            Object.entries(this.qf).forEach(([k, v]) => { if (v) p.set(k, v); });
            p.set('insurer', insurer);
            window.location.href = '{{ route("quotations.create") }}?' + p.toString();
        },

        resetQuoteForm() {
            this.quoteStep = 1;
            this.qf = { salutation:'', first_name:'', middle_name:'', last_name:'',
                phone:'', email:'', reg:'', chassis:'', make:'', make_other:'',
                model:'', body:'', year:'', color:'', color_other:'', value:'' };
            this.premiums = { ga: 0, sanlam: 0 };
        },

        openApplyFor(userId, userName) {
            this.selectedUser  = userId;
            this.applyUserName = userName;
            this.applyModal    = true;
        },

        rowVisible(el) {
            const searchStr  = (el.dataset.search || '').toLowerCase();
            const statusStr  = (el.dataset.status || '').toLowerCase();
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
                    datasets: [{ label: 'Quotations', data: this.companyData.map(d => d.count),
                        backgroundColor: ['#1E40AF','#F59E0B','#10B981','#8B5CF6','#EF4444'],
                        borderRadius: 5, borderSkipped: false }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero:true, ticks:{ precision:0, font:{size:10}, color:'#9ca3af' }, grid:{color:'rgba(0,0,0,0.04)'} },
                        x: { ticks:{ font:{size:10,weight:'600'}, color:'#6b7280' }, grid:{display:false} }
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
                    datasets: [{ data: this.companyData.map(d => d.total_premium || 0),
                        backgroundColor: ['#1E40AF','#F59E0B','#10B981','#8B5CF6','#EF4444'],
                        borderWidth: 2, borderColor: '#fff', hoverOffset: 5 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '68%',
                    plugins: { legend: { position:'bottom', labels:{ font:{size:10}, color:'#6b7280', boxWidth:8, padding:10 } } }
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
                    datasets: [{ label:'Quotations', data: this.trendData.map(d => d.count),
                        borderColor:'#1E40AF', backgroundColor:'rgba(30,64,175,0.07)',
                        borderWidth:2, pointBackgroundColor:'#1E40AF', pointRadius:2.5,
                        fill:true, tension:0.4 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero:true, ticks:{ precision:0, font:{size:10}, color:'#9ca3af' }, grid:{color:'rgba(0,0,0,0.04)'} },
                        x: { ticks:{ font:{size:9}, color:'#9ca3af', maxRotation:45 }, grid:{display:false} }
                    }
                }
            });
        }
    }
}
</script>
@endpush
</x-app-layout>