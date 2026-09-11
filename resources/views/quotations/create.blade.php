<x-app-layout>
<div class="space-y-6 pt-10 fade-in max-w-10xl mx-auto">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Motor Insurance</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">
                    @if(request('on_behalf_of') && isset($targetUser))
                        New Quotation — <span class="text-blue-300">{{ $targetUser->name }}</span>
                    @else
                        Get Your <span class="text-blue-300">Motor Insurance Quote</span>
                    @endif
                </h1>
                <p class="mt-0.5 text-xs text-slate-400">Complete the steps below to generate a personalised insurance quotation.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if(request('on_behalf_of') && isset($targetUser))
                <div class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Applying as admin for <strong>{{ $targetUser->name }}</strong>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── USER SELECTOR ─────────────────────────────────────────── --}}
    @php $isAdminUser = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'); @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 whitespace-nowrap">Generate quotation for:</div>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="generate_for" value="self" onchange="toggleUserSelector('self')"
                               {{ !request('on_behalf_of') ? 'checked' : '' }}
                               class="accent-blue-600">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Myself ({{ auth()->user()->name }})</span>
                    </label>
                    @if($isAdminUser)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="generate_for" value="other" onchange="toggleUserSelector('other')"
                               {{ request('on_behalf_of') ? 'checked' : '' }}
                               class="accent-blue-600">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Another user</span>
                    </label>
                    @endif
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="generate_for" value="new" onchange="toggleUserSelector('new')"
                               class="accent-blue-600">
                        <span class="text-xs text-gray-700 dark:text-gray-300">New Customer</span>
                    </label>
                </div>
            </div>
            @if($isAdminUser)
            <div id="user-selector-dropdown" class="mt-3 {{ request('on_behalf_of') ? '' : 'hidden' }}">
                <select id="f-on-behalf-of" onchange="onUserSelected(this.value)"
                        class="w-full max-w-xs px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">— Select a user —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ (request('on_behalf_of') == $u->id) ? 'selected' : '' }}
                                data-first="{{ $u->first_name ?? '' }}"
                                data-last="{{ $u->last_name ?? '' }}"
                                data-email="{{ $u->email ?? '' }}"
                                data-phone="{{ $u->mobile ?? '' }}">
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div id="new-customer-note" class="mt-3 hidden">
                <p class="text-[11px] text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">
                    This customer isn't a registered user. Fill in their name, phone and email in the Personal Details step below — the quotation will be saved against those details directly, with no account created.
                </p>
            </div>
            <input type="hidden" id="f-is-new-customer" value="">
        </div>
    </div>
    <div class="h-4"></div>

    {{-- ─── VEHICLE TYPE SELECTOR ─────────────────────────────────────────--}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Motor Insurance</p>
            <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">What type of vehicle do you own?</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Select your vehicle category to get a tailored insurance quote.</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" id="vehicle-type-select">
            <button type="button" onclick="selectVehicleType('private')" id="vt-card-private"
                class="vehicle-type-card text-left flex items-start gap-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-5 transition hover:shadow-md hover:-translate-y-0.5 cursor-pointer">
                <div class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 0a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-8 0H4a2 2 0 00-2 2v6a2 2 0 002 2h2m10-8h4a2 2 0 012 2v6a2 2 0 01-2 2h-2"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white">Private Car Owner</p>
                    <p class="text-[10px] text-gray-400 mt-0.5 leading-relaxed">I own a personal / private motor vehicle and need comprehensive insurance.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-blue-600 dark:text-blue-400">Get Started →</span>
                </div>
            </button>
            <button type="button" onclick="selectVehicleType('psv')" id="vt-card-psv"
                class="vehicle-type-card text-left flex items-start gap-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-5 transition hover:shadow-md hover:-translate-y-0.5 cursor-pointer">
                <div class="w-11 h-11 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 0a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-8 0H4a2 2 0 00-2 2v6a2 2 0 002 2h2m10-8h4a2 2 0 012 2v6a2 2 0 01-2 2h-2m-3 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-white">Public Service Vehicle (PSV) Owner</p>
                    <p class="text-[10px] text-gray-400 mt-0.5 leading-relaxed">I own a matatu, bus, taxi or other public service vehicle and need PSV insurance.</p>
                    <span class="inline-flex items-center gap-1 mt-2 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">Get Started →</span>
                </div>
            </button>
        </div>
    </div>
    <div class="h-4"></div>

    {{-- ─── FORM CARD ─────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Hidden fields for signed form token, admin and vehicle type --}}
        <input type="hidden" id="f-signed" value="{{ $signedToken }}">
        <input type="hidden" id="f-user-id" value="{{ $targetUser->id ?? '' }}">
        <input type="hidden" id="f-on-behalf-of-hidden" value="{{ request('on_behalf_of', '') }}">
        <input type="hidden" id="f-vehicle-type" value="{{ $prefill['vehicle_type'] ?? 'private' }}">

        {{-- Step Indicators --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <div class="flex gap-2">
                <div class="flex-1 min-w-0 px-3 py-2.5 rounded-lg border text-center cursor-pointer transition step-tab active" id="step-tab-1" onclick="goStep(1)">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 step-num">Step 1</div>
                    <div class="text-xs font-semibold text-blue-700 dark:text-blue-300 step-title">Personal Details</div>
                    <div class="text-[10px] text-gray-400">Contact information</div>
                </div>
                <div class="flex-1 min-w-0 px-3 py-2.5 rounded-lg border text-center cursor-pointer transition step-tab" id="step-tab-2" onclick="goStep(2)">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-gray-400 step-num">Step 2</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 step-title">Vehicle Details</div>
                    <div class="text-[10px] text-gray-400">Car information</div>
                </div>
                <div class="flex-1 min-w-0 px-3 py-2.5 rounded-lg border text-center cursor-pointer transition step-tab" id="step-tab-3" onclick="goStep(3)">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-gray-400 step-num">Step 3</div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 step-title">Quote Generation</div>
                    <div class="text-[10px] text-gray-400">Premium estimates</div>
                </div>
            </div>
        </div>

        {{-- ── Step 1: Personal Details ── --}}
        <div class="p-6 step-panel active" id="panel-1">
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Personal Details</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Please fill in the contact details of the insured party.</p>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-salutation">Salutation <span class="text-rose-500">*</span></label>
                    <select id="f-salutation" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Select...</option>
                        <option {{ $prefill['salutation'] === 'Mr.' ? 'selected' : '' }}>Mr.</option>
                        <option {{ $prefill['salutation'] === 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                        <option {{ $prefill['salutation'] === 'Miss' ? 'selected' : '' }}>Miss</option>
                        <option {{ $prefill['salutation'] === 'Dr.' ? 'selected' : '' }}>Dr.</option>
                        <option {{ $prefill['salutation'] === 'Prof.' ? 'selected' : '' }}>Prof.</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-first">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="f-first" value="{{ $prefill['first_name'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. John">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-middle">Middle Name</label>
                    <input type="text" id="f-middle" value="{{ $prefill['middle_name'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-last">Last Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="f-last" value="{{ $prefill['last_name'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. Doe">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-phone">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="tel" id="f-phone" value="{{ $prefill['phone'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="07 XX XXX XXX">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-email">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" id="f-email" value="{{ $prefill['email'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="email@example.com">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Cancel</a>
                <button onclick="goStep(2)" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Continue to Vehicle Details →</button>
            </div>
        </div>

        {{-- ── Step 2: Vehicle Details ── --}}
        <div class="p-6 step-panel" id="panel-2">
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Vehicle Details</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Provide accurate vehicle information for a precise premium calculation.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-reg">Registration Number <span class="text-rose-500">*</span></label>
                    <input type="text" id="f-reg" value="{{ $prefill['reg'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. KDH 724V">
                </div>
                <div id="f-pax-group" style="display:{{ ($prefill['vehicle_type'] ?? 'private') === 'psv' ? 'block' : 'none' }}">
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-pax">Number of Passengers (Pax) <span class="text-rose-500">*</span></label>
                    <input type="number" id="f-pax" min="1" value="1" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. 14">
                    <p class="text-[10px] text-gray-400 mt-1">Used to calculate Passenger Legal Liability (PLL)</p>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-chassis">Chassis / Frame Number</label>
                    <input type="text" id="f-chassis" value="{{ $prefill['chassis'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. WVWZZZ1KZ9W123456">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-engine">Engine No</label>
                    <input type="text" id="f-engine" value="{{ $prefill['engine'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="e.g. CBA1234567">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Make of Vehicle <span class="text-rose-500">*</span></label>
                    <div class="vehicle-search-wrap-dash">
                        <input type="text" id="f-make" value="{{ $prefill['make'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Search vehicle make..." autocomplete="off">
                        <div class="vehicle-dropdown-dash" id="f-make-dropdown"></div>
                    </div>
                    <input type="hidden" id="f-make-value" value="{{ $prefill['make'] }}">
                    <input type="text" id="f-make-other" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none mt-1.5" placeholder="Type your vehicle make..." style="display:none">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Model Name <span class="text-rose-500">*</span></label>
                    <div class="vehicle-search-wrap-dash">
                        <input type="text" id="f-model" value="{{ $prefill['model'] }}" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Select a make first..." autocomplete="off" disabled>
                        <div class="vehicle-dropdown-dash" id="f-model-dropdown"></div>
                    </div>
                    <input type="hidden" id="f-model-value" value="{{ $prefill['model'] }}">
                    <input type="text" id="f-model-other" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none mt-1.5" placeholder="Type your vehicle model..." style="display:none">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1" for="f-year">Year of Manufacture <span class="text-rose-500">*</span></label>
                    <select id="f-year" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none"></select>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Value of Vehicle (KES) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">KES</span>
                        <input type="text" id="f-value" value="{{ $prefill['value'] ? number_format((float) $prefill['value']) : '' }}" class="w-full pl-11 pr-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none" placeholder="800,000" autocomplete="off">
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1" id="f-value-hint">Enter between KES 800,000 and KES 1,300,000</div>
                    <div class="text-[10px] text-red-500 font-semibold mt-1" id="f-value-error" style="display:none"></div>
                </div>
            </div>
            <div class="flex justify-between gap-2 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button onclick="goStep(1)" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Back</button>
                <button onclick="goStep(3)" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Continue to Quote Generation →</button>
            </div>
        </div>

        {{-- ── Step 3: Quote Generation ── --}}
        <div class="p-6 step-panel" id="panel-3">
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-800 dark:text-white">Premium Estimate</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Select an insurer card to open its full quotation, or compare all side by side.</p>
            </div>

            {{-- Empty state --}}
            <div id="premium-preview-empty" class="text-center py-10 border-1.5 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-400 mb-4">
                Go back to Step 2 and enter the <strong>vehicle value</strong> to see premium estimates.
            </div>

            {{-- Quote cards --}}
            <div id="premium-preview" style="display:none" class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="companyCardsContainer">
                    @foreach($companies as $company)
                    @php
                        $rate = $rates[$company->id]->first() ?? null;
                        $rateVal = $rate ? (float) $rate->percentage / 100 : 0.035;
                        $colors = ['#1756c8', '#7c3aed', '#0f7a65', '#d4860e', '#c0392b'];
                        $colorIdx = $loop->index % count($colors);
                    @endphp
                    <div onclick="openQuotePage('{{ $company->slug }}')" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition flex flex-col items-center gap-1.5" data-slug="{{ $company->slug }}" data-rate="{{ $rateVal }}">
                        <div class="h-9 flex items-center justify-center mb-1">
                            <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}" class="max-h-8 max-w-[90px] object-contain"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="text-xs font-extrabold" style="color:{{ $colors[$colorIdx] }};display:none">{{ $company->getInitials() }}</div>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider" style="color:{{ $colors[$colorIdx] }}">{{ $company->name }}</div>
                        <div class="text-[10px] text-gray-400" id="rate-{{ $company->slug }}">@ {{ $rateVal * 100 }}%</div>
                        <div class="text-base font-extrabold premium-amount" style="color:{{ $colors[$colorIdx] }}" id="prev-{{ $company->slug }}">—</div>
                        <div class="w-full text-[10px] font-bold text-white rounded-md py-1.5 mt-1" style="background:{{ $colors[$colorIdx] }}">View Quote →</div>
                    </div>
                    @endforeach

                    {{-- Compare All --}}
                    <div onclick="openQuotePage('ALL')" class="bg-white dark:bg-gray-800 border-2 border-red-300 dark:border-red-700 rounded-xl p-4 text-center cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition flex flex-col items-center gap-1.5">
                        <div class="h-9 flex items-center justify-center mb-1">
                            <img src="{{ asset('images/benchmarking.png') }}" alt="Compare All" class="max-h-8 max-w-[90px] object-contain"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="text-xs" style="display:none">📊</div>
                        </div>
                        <div class="text-[10px] font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">Compare All</div>
                        <div class="text-[10px] text-gray-400">All insurers</div>
                        <div class="text-base font-extrabold text-red-700 dark:text-red-400" id="compare-all-count">{{ $companies->count() }} Quotes</div>
                        <div class="w-full text-[10px] font-bold text-white bg-red-700 rounded-md py-1.5 mt-1">Compare →</div>
                    </div>
                </div>

                {{-- Summary row --}}
                <div id="qf-summary" class="flex items-center gap-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-5 py-3" style="display:none">
                    <div class="flex-1 text-center">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Vehicle Value</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white" id="sum-value">—</span>
                    </div>
                    <div class="w-px h-8 bg-blue-200 dark:bg-blue-700"></div>
                    <div class="flex-1 text-center">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Lowest</span>
                        <span class="text-sm font-bold text-emerald-600" id="sum-lowest">—</span>
                    </div>
                    <div class="w-px h-8 bg-blue-200 dark:bg-blue-700"></div>
                    <div class="flex-1 text-center">
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Highest</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-white" id="sum-highest">—</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-start gap-2 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button onclick="goStep(2)" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">← Back to Vehicle Details</button>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<style>
.vehicle-search-wrap-dash{position:relative}
.vehicle-dropdown-dash{display:none;position:absolute;top:100%;left:0;right:0;z-index:50;background:#fff;border:1px solid #d1d5db;border-radius:0 0 8px 8px;max-height:220px;overflow-y:auto;box-shadow:0 4px 16px rgba(0,0,0,.1)}
.vehicle-dropdown-dash.open{display:block}
.vehicle-dropdown-dash .vehicle-option{padding:8px 12px;font-size:12px;cursor:pointer;border-bottom:1px solid #f3f4f6;transition:background .15s;color:#374151}
.vehicle-dropdown-dash .vehicle-option:last-child{border-bottom:none}
.vehicle-dropdown-dash .vehicle-option:hover{background:#eff6ff;color:#2563eb}
.vehicle-dropdown-dash .vehicle-option-other{border-top:1px solid #e5e7eb;color:#9ca3af;font-style:italic}
.vehicle-dropdown-dash .vehicle-option-other:hover{color:#2563eb;background:#fef3c7}
</style>
<script>
// ── Admin: user selector toggle ──
function toggleUserSelector(val) {
    var dd = document.getElementById('user-selector-dropdown');
    var note = document.getElementById('new-customer-note');
    var hidden = document.getElementById('f-user-id');
    var isNewCustomer = document.getElementById('f-is-new-customer');

    if (dd) dd.classList.add('hidden');
    note.classList.add('hidden');

    if (val === 'self') {
        if (hidden) hidden.value = '';
        if (isNewCustomer) isNewCustomer.value = '';
    } else if (val === 'other') {
        if (dd) dd.classList.remove('hidden');
        if (isNewCustomer) isNewCustomer.value = '';
        var onBehalfSelect = document.getElementById('f-on-behalf-of');
        if (onBehalfSelect) onBehalfSelect.focus();
    } else if (val === 'new') {
        note.classList.remove('hidden');
        if (hidden) hidden.value = '';
        if (isNewCustomer) isNewCustomer.value = '1';
        var onBehalfHidden = document.getElementById('f-on-behalf-of-hidden');
        if (onBehalfHidden) onBehalfHidden.value = '';
        ['f-salutation', 'f-first', 'f-middle', 'f-last', 'f-phone', 'f-email'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        var firstEl = document.getElementById('f-first');
        if (firstEl) firstEl.focus();
    }
}

// ── Vehicle type selection ──
function selectVehicleType(type) {
    var hidden = document.getElementById('f-vehicle-type');
    if (hidden) hidden.value = type;
    var paxGroup = document.getElementById('f-pax-group');
    if (paxGroup) paxGroup.style.display = type === 'psv' ? 'block' : 'none';
    ['private', 'psv'].forEach(function (t) {
        var card = document.getElementById('vt-card-' + t);
        if (!card) return;
        var active = t === type;
        card.classList.toggle('border-blue-500', active);
        card.classList.toggle('bg-blue-50', active);
        card.classList.toggle('dark:bg-blue-900/20', active);
        card.classList.toggle('border-gray-200', !active);
        card.classList.toggle('dark:border-gray-700', !active);
    });
    document.getElementById('vehicle-type-select').scrollIntoView({ behavior: 'smooth', block: 'start' });
    updatePremiumPreview();
}

function onUserSelected(userId) {
    var hidden = document.getElementById('f-user-id');
    if (!userId) {
        if (hidden) hidden.value = '';
        return;
    }
    if (hidden) hidden.value = userId;
    var opt = document.querySelector('#f-on-behalf-of option[value="' + userId + '"]');
    if (!opt) return;
    var first = document.getElementById('f-first');
    var last = document.getElementById('f-last');
    var email = document.getElementById('f-email');
    var phone = document.getElementById('f-phone');
    if (first) first.value = opt.dataset.first || '';
    if (last) last.value = opt.dataset.last || '';
    if (email) email.value = opt.dataset.email || '';
    if (phone) phone.value = opt.dataset.phone || '';
}

// ── Populate year dropdown ──
(function () {
    var sel = document.getElementById('f-year');
    var max = new Date().getFullYear();
    for (var y = max; y >= 1990; y--) {
        var opt = document.createElement('option');
        opt.value = y; opt.textContent = y;
        sel.appendChild(opt);
    }
})();

// ── Step navigation ──
function goStep(n) {
    [1,2,3].forEach(function(i) {
        var tab = document.getElementById('step-tab-' + i);
        var panel = document.getElementById('panel-' + i);
        var num = tab.querySelector('.step-num');
        var title = tab.querySelector('.step-title');
        tab.classList.remove('active', 'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        tab.classList.add('border-gray-200', 'dark:border-gray-700');
        panel.classList.remove('active');
        panel.style.display = 'none';
        if (i < n) {
            tab.classList.add('border-emerald-300', 'bg-emerald-50', 'dark:bg-emerald-900/20');
            num.className = 'text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 step-num';
            title.className = 'text-xs font-semibold text-emerald-700 dark:text-emerald-300 step-title';
        } else {
            num.className = 'text-[9px] font-bold uppercase tracking-wider text-gray-400 step-num';
            title.className = 'text-xs font-semibold text-gray-500 dark:text-gray-400 step-title';
        }
    });
    var tab = document.getElementById('step-tab-' + n);
    var panel = document.getElementById('panel-' + n);
    tab.classList.remove('border-gray-200', 'dark:border-gray-700', 'border-emerald-300', 'bg-emerald-50', 'dark:bg-emerald-900/20');
    tab.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    var num = tab.querySelector('.step-num');
    var title = tab.querySelector('.step-title');
    num.className = 'text-[9px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 step-num';
    title.className = 'text-xs font-semibold text-blue-700 dark:text-blue-300 step-title';
    panel.classList.add('active');
    panel.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('panel-1').style.display = 'block';
    document.getElementById('panel-2').style.display = 'none';
    document.getElementById('panel-3').style.display = 'none';
    var preSelected = document.getElementById('f-on-behalf-of-hidden')?.value;
    if (preSelected && document.getElementById('f-on-behalf-of')) {
        document.getElementById('f-on-behalf-of').value = preSelected;
        onUserSelected(preSelected);
    }

    // ── Prefill helpers ──
    var prefillYear = "{{ $prefill['year'] }}";
    var prefillMake = "{{ $prefill['make'] }}";
    var prefillModel = "{{ $prefill['model'] }}";

    var yearSel = document.getElementById('f-year');
    if (prefillYear && yearSel) yearSel.value = prefillYear;

    if (prefillMake) {
        var mi = document.getElementById('f-model');
        if (mi) { mi.disabled = false; mi.placeholder = 'Search model...'; }
    }
    if (prefillMake && prefillModel) {
        var mv = document.getElementById('f-model-value');
        if (mv) mv.value = prefillModel;
    }

    var vt = document.getElementById('f-vehicle-type')?.value || 'private';
    if (vt) selectVehicleType(vt);

    var valEl = document.getElementById('f-value');
    if (valEl && valEl.value) {
        updatePremiumPreview();
    }
});

// ── Other color toggle ──
function toggleOtherColor(sel) {
    var box = document.getElementById('f-color-other');
    if (sel.value === 'Other') { box.style.display = ''; box.focus(); }
    else { box.style.display = 'none'; box.value = ''; }
}

// ── Premium preview (real backend PremiumCalculationService via AJAX) ──
function fmt(n) { return 'KES ' + n.toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0}); }

var _premiumPreviewDebounce = null;
var _premiumPreviewRequestId = 0;

function updatePremiumPreview() {
    var raw = (document.getElementById('f-value').value || '').replace(/,/g, '');
    var val = parseFloat(raw) || 0;
    var empty = document.getElementById('premium-preview-empty');
    var preview = document.getElementById('premium-preview');
    var summary = document.getElementById('qf-summary');

    if (val < 1) {
        empty.style.display = '';
        preview.style.display = 'none';
        summary.style.display = 'none';
        return;
    }
    empty.style.display = 'none';
    preview.style.display = '';

    clearTimeout(_premiumPreviewDebounce);
    _premiumPreviewDebounce = setTimeout(function() { fetchPremiumPreview(val); }, 350);
}

function fetchPremiumPreview(val) {
    var vehicleType = (document.getElementById('f-vehicle-type') || {}).value || 'private';
    var numPassengers = parseInt((document.getElementById('f-pax') || {}).value, 10) || 1;
    var requestId = ++_premiumPreviewRequestId;
    var cards = document.querySelectorAll('#companyCardsContainer > div[data-slug]');

    fetch('{{ route("quotations.compare-all.calculate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ vehicle_type: vehicleType, car_valuation: val, num_passengers: numPassengers }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (requestId !== _premiumPreviewRequestId) return; // a newer request superseded this one
        var results = data.results || [];
        var bySlug = {};
        results.forEach(function(r) { bySlug[r.company.slug] = r.breakdown; });

        var lowest = Infinity, highest = -Infinity;
        cards.forEach(function(card) {
            var slug = card.dataset.slug;
            var breakdown = bySlug[slug];
            if (!breakdown) { card.style.display = 'none'; return; }
            card.style.display = '';
            var amt = breakdown.total_premium;
            var el = document.getElementById('prev-' + slug);
            var rateEl = document.getElementById('rate-' + slug);
            if (el) el.textContent = fmt(amt);
            if (rateEl) rateEl.textContent = '@ ' + breakdown.premium_rate + '%';
            if (amt < lowest) lowest = amt;
            if (amt > highest) highest = amt;
        });

        var countEl = document.getElementById('compare-all-count');
        if (countEl) countEl.textContent = results.length + ' Quotes';

        document.getElementById('sum-value').textContent   = fmt(val);
        document.getElementById('sum-lowest').textContent  = fmt(lowest !== Infinity ? lowest : 0);
        document.getElementById('sum-highest').textContent = fmt(highest !== -Infinity ? highest : 0);
        document.getElementById('qf-summary').style.display = '';
    })
    .catch(function() {});
}

// ── Open quote page ──
function openQuotePage(insurer) {
    var valFieldEl = document.getElementById('f-value');
    var valRaw = (valFieldEl.value || '').replace(/,/g, '');
    var valNum = parseInt(valRaw, 10) || 0;
    var valHint = document.getElementById('f-value-hint');
    var valErr = document.getElementById('f-value-error');
    if (valNum < 800000 || valNum > 1300000) {
        if (valErr) {
            valErr.textContent = 'Vehicle value must be between KES 800,000 and KES 1,300,000';
            valErr.style.display = '';
        }
        valFieldEl.style.borderColor = '#e53e3e';
        if (valHint) valHint.style.display = 'none';
        valFieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        valFieldEl.focus();
        return;
    }
    if (valErr) valErr.style.display = 'none';
    valFieldEl.style.borderColor = '';
    if (valHint) valHint.style.display = '';

    var userId = document.getElementById('f-user-id')?.value || null;
    var makeHidden = (document.getElementById('f-make-value') || {}).value || '';
    var makeOther  = (document.getElementById('f-make-other') || {}).value || '';
    var makeInput  = (document.getElementById('f-make') || {}).value || '';
    var make       = makeHidden === 'Other' ? makeOther : (makeHidden || makeInput);
    var modelHidden = (document.getElementById('f-model-value') || {}).value || '';
    var modelOther  = (document.getElementById('f-model-other') || {}).value || '';
    var modelInput  = (document.getElementById('f-model') || {}).value || '';
    var model       = modelHidden === 'Other' ? modelOther : (modelHidden || modelInput);
    var colorRaw   = (document.getElementById('f-color') || {}).value || '';
    var colorOther = (document.getElementById('f-color-other') || {}).value || '';
    var color      = colorRaw === 'Other' ? colorOther : colorRaw;
    var rawValue   = (document.getElementById('f-value').value || '').replace(/,/g, '');
    var data = {
        insurer: insurer,
        salutation : document.getElementById('f-salutation').value,
        first      : document.getElementById('f-first').value,
        middle     : document.getElementById('f-middle').value,
        last       : document.getElementById('f-last').value,
        phone      : document.getElementById('f-phone').value,
        email      : document.getElementById('f-email').value,
        reg        : document.getElementById('f-reg').value,
        chassis    : document.getElementById('f-chassis').value,
        engine     : document.getElementById('f-engine').value,
        make       : make,
        model      : model,
        body       : document.getElementById('f-body').value,
        year       : document.getElementById('f-year').value,
        color      : color,
        value      : rawValue,
        vehicle_type : (document.getElementById('f-vehicle-type') || {}).value || 'private',
        num_passengers : parseInt((document.getElementById('f-pax') || {}).value) || 1,
        _signed    : document.getElementById('f-signed').value,
        userId: userId,
        is_new_customer: (document.getElementById('f-is-new-customer') || {}).value === '1',
    };
    sessionStorage.setItem('micQuoteData', JSON.stringify(data));
    clearDraft();
    var params = '?insurer=' + insurer;
    if (userId) params += '&on_behalf_of=' + userId;
    var destination = insurer === 'ALL' ? "{{ route('quotations.compare-all') }}" : "{{ route('quotations.preview') }}";
    window.location.href = destination + params;
}

// ── VEHICLE SEARCH ──
(function() {
    var makesCache = [];
    var modelsCache = {};

    function fetchJSON(url, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function() { if (xhr.status === 200) cb(JSON.parse(xhr.responseText)); };
        xhr.send();
    }

    function setupSearch(inputId, dropdownId, hiddenId, otherId, opts) {
        var input = document.getElementById(inputId);
        var dropdown = document.getElementById(dropdownId);
        var hidden = document.getElementById(hiddenId);
        var other = document.getElementById(otherId);
        if (!input || !dropdown) return null;

        var items = opts.items || [];
        var onSelect = opts.onSelect || function(){};
        var onOther = opts.onOther || function(){};

        input.addEventListener('input', function() {
            var val = this.value.toLowerCase().trim();
            if (!val) { dropdown.innerHTML = ''; dropdown.classList.remove('open'); return; }
            var filtered = items.filter(function(i) { return i.toLowerCase().indexOf(val) > -1; });
            var html = '';
            filtered.forEach(function(i) { html += '<div class="vehicle-option" data-value="' + i.replace(/'/g, '&#39;') + '">' + i + '</div>'; });
            html += '<div class="vehicle-option vehicle-option-other" data-value="__other__">+ Other</div>';
            dropdown.innerHTML = html;
            dropdown.classList.add('open');
        });

        dropdown.addEventListener('click', function(e) {
            var opt = e.target.closest('.vehicle-option');
            if (!opt) return;
            var val = opt.dataset.value;
            if (val === '__other__') {
                input.value = '';
                hidden.value = 'Other';
                if (other) { other.style.display = ''; other.focus(); }
                onOther();
            } else {
                input.value = val;
                hidden.value = val;
                if (other) { other.style.display = 'none'; other.value = ''; }
                onSelect(val);
            }
            dropdown.classList.remove('open');
        });

        input.addEventListener('blur', function() { setTimeout(function() { dropdown.classList.remove('open'); }, 200); });
        input.addEventListener('focus', function() { if (this.value.trim()) this.dispatchEvent(new Event('input')); });

        return { setItems: function(newItems) { items = newItems; }, getItems: function() { return items; } };
    }

    var makeSearch = null;
    var modelSearch = null;

    function initVehicleSearch() {
        fetchJSON('/api/vehicle-makes', function(makes) {
            makesCache = makes;
            if (makeSearch) makeSearch.setItems(makes);
        });

        makeSearch = setupSearch('f-make', 'f-make-dropdown', 'f-make-value', 'f-make-other', {
            items: makesCache,
            onSelect: function(val) {
                var mi = document.getElementById('f-model');
                if (mi) { mi.disabled = false; mi.placeholder = 'Search model...'; mi.value = ''; }
                var mo = document.getElementById('f-model-other');
                if (mo) { mo.style.display = 'none'; mo.value = ''; }
                var mh = document.getElementById('f-model-value');
                if (mh) mh.value = '';
                var md = document.getElementById('f-model-dropdown');
                if (md) { md.innerHTML = ''; md.classList.remove('open'); }
                fetchJSON('/api/vehicle-models/' + encodeURIComponent(val), function(models) {
                    modelsCache[val] = models;
                    if (modelSearch) modelSearch.setItems(models);
                });
            },
            onOther: function() {
                var mi = document.getElementById('f-model');
                if (mi) { mi.disabled = false; mi.placeholder = 'Type your vehicle model...'; mi.value = ''; }
                var mh = document.getElementById('f-model-value');
                if (mh) mh.value = '';
                if (modelSearch) modelSearch.setItems([]);
            }
        });

        modelSearch = setupSearch('f-model', 'f-model-dropdown', 'f-model-value', 'f-model-other', {
            items: [],
            onSelect: function(){},
            onOther: function(){}
        });
    }

    if (document.getElementById('f-make')) initVehicleSearch();
})();

// ── Passengers (PLL) affects premium preview for PSV ──
(function() {
    var paxInput = document.getElementById('f-pax');
    if (paxInput) paxInput.addEventListener('input', updatePremiumPreview);
})();

// ── VALUE FORMATTING & VALIDATION ──
(function() {
    var valInput = document.getElementById('f-value');
    if (!valInput) return;

    valInput.addEventListener('input', function() {
        var cursor = this.selectionStart;
        var raw = this.value.replace(/[^0-9]/g, '');
        if (raw === '') { this.value = ''; return; }
        var formatted = parseInt(raw, 10).toLocaleString('en-KE');
        this.value = formatted;
        this.setSelectionRange(cursor + (formatted.length - this.value.length), cursor + (formatted.length - this.value.length));
        updatePremiumPreview();
    });

    valInput.addEventListener('blur', function() {
        var raw = this.value.replace(/,/g, '');
        var val = parseInt(raw, 10) || 0;
        var hint = document.getElementById('f-value-hint');
        var err = document.getElementById('f-value-error');
        if (!err) return;
        if (val > 0 && (val < 800000 || val > 1300000)) {
            err.textContent = 'Vehicle value must be between KES 800,000 and KES 1,300,000';
            err.style.display = '';
            this.style.borderColor = '#e53e3e';
            if (hint) hint.style.display = 'none';
        } else {
            err.style.display = 'none';
            this.style.borderColor = '';
            if (hint) hint.style.display = '';
        }
    });
})();

// ── Session Storage for draft resilience ──
(function() {
    var STORAGE_KEY = 'micQuoteDraft';
    var fields = [
        'f-salutation', 'f-first', 'f-middle', 'f-last', 'f-phone', 'f-email',
        'f-reg', 'f-chassis', 'f-engine', 'f-make', 'f-make-value', 'f-make-other',
        'f-model', 'f-model-value', 'f-model-other', 'f-body',
        'f-year', 'f-color', 'f-color-other', 'f-value', 'f-signed', 'f-vehicle-type', 'f-pax'
    ];

    function saveDraft() {
        var data = {};
        fields.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) data[id.replace('f-', '')] = el.value;
        });
        data.savedAt = new Date().toISOString();
        try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data)); } catch(e) {}
    }

    function restoreDraft(data) {
        fields.forEach(function(id) {
            var key = id.replace('f-', '');
            var el = document.getElementById(id);
            if (el && data[key] !== undefined) el.value = data[key];
        });
        var makeOther = document.getElementById('f-make-other');
        var colorOther = document.getElementById('f-color-other');
        var modelOther = document.getElementById('f-model-other');
        var makeHidden = document.getElementById('f-make-value');
        var modelHidden = document.getElementById('f-model-value');
        if (makeOther && makeHidden && makeHidden.value === 'Other') makeOther.style.display = '';
        if (modelOther && modelHidden && modelHidden.value === 'Other') modelOther.style.display = '';
        if (colorOther && document.getElementById('f-color').value === 'Other') colorOther.style.display = '';
        updatePremiumPreview();
    }

    function clearDraft() {
        try { sessionStorage.removeItem(STORAGE_KEY); } catch(e) {}
    }
    window.clearDraft = clearDraft;

    try {
        var saved = sessionStorage.getItem(STORAGE_KEY);
        if (saved) {
            var data = JSON.parse(saved);
            var container = document.querySelector('.step-panel')?.parentElement;
            if (container && data.savedAt) {
                var banner = document.createElement('div');
                banner.className = 'mx-6 mt-4 mb-0 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 flex items-center justify-between gap-3 text-xs text-amber-800 dark:text-amber-300';
                banner.innerHTML = [
                    '<span>You have a saved draft from ' + new Date(data.savedAt).toLocaleString() + '.</span>',
                    '<div class="flex gap-2 shrink-0">',
                    '<button class="restore-draft px-3 py-1.5 bg-amber-600 text-white rounded-lg text-[10px] font-semibold hover:bg-amber-700 transition">Restore</button>',
                    '<button class="discard-draft px-3 py-1.5 border border-amber-600 text-amber-700 dark:text-amber-300 rounded-lg text-[10px] font-semibold hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">Discard</button>',
                    '</div>'
                ].join('');
                container.insertBefore(banner, container.firstChild);
                banner.querySelector('.restore-draft').addEventListener('click', function() { restoreDraft(data); banner.remove(); });
                banner.querySelector('.discard-draft').addEventListener('click', function() { clearDraft(); banner.remove(); });
            }
        }
    } catch(e) {}

    var saveTimer;
    document.addEventListener('input', function(e) {
        if (fields.indexOf(e.target.id) > -1) {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveDraft, 300);
        }
    });
    document.addEventListener('change', function(e) {
        if (fields.indexOf(e.target.id) > -1) saveDraft();
    });
})();
</script>
@endpush
</x-app-layout>
