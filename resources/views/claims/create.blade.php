<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Claims</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Log New Claim</h1>
                <p class="mt-0.5 text-xs text-slate-400">File a new insurance claim</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 fade-in">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('claims.store') }}" id="claimForm">
    @csrf

    {{-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"> --}}
        {{-- <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Claim Details</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Select who you are logging this claim for</p>
        </div> --}}
        {{-- <div class="p-6 space-y-6"> --}}

            {{-- Claim For --}}
            {{-- <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Log claim for *</label>
                <select name="claim_for" id="claim_for" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="self" {{ old('claim_for') == 'self' ? 'selected' : '' }}>Myself</option>
                    <option value="user" {{ old('claim_for') == 'user' ? 'selected' : '' }}>Another User</option>
                    @if($isAdmin)
                        <option value="admin" {{ old('claim_for') == 'admin' ? 'selected' : '' }}>Another Admin</option>
                    @endif
                    <option value="customer" {{ old('claim_for') == 'customer' ? 'selected' : '' }}>Customer (Not in system)</option>
                </select>
            </div> --}}

            {{-- <div id="user-select" class="{{ in_array(old('claim_for'), ['user', 'admin']) ? '' : 'hidden' }}">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Select User</label>
                <select name="on_behalf_of" id="on_behalf_of" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select user...</option>
                    @foreach(($users ?? []) as $u)
                        <option value="{{ $u->id }}" data-name="{{ $u->first_name }} {{ $u->last_name }}" data-email="{{ $u->email }}" data-phone="{{ $u->mobile ?? '' }}" {{ old('on_behalf_of') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div> --}}

            {{-- <div id="admin-select" class="{{ old('claim_for') == 'admin' ? '' : 'hidden' }}">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Select Admin</label>
                <select name="on_behalf_of" id="on_behalf_of_admin" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select admin...</option>
                    @foreach($admins as $u)
                        <option value="{{ $u->id }}" data-name="{{ $u->first_name }} {{ $u->last_name }}" data-email="{{ $u->email }}" data-phone="{{ $u->mobile ?? '' }}" {{ old('on_behalf_of') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div> --}}

            {{-- Customer details --}}
            {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Customer Name <span id="customer-required">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $self['name'] ?? '') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Customer Email <span id="customer-email-required">*</span></label>
                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $self['email'] ?? '') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Customer Phone <span id="customer-phone-required">*</span></label>
                    <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $self['phone'] ?? '') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div> --}}

        {{-- </div> --}}
    {{-- </div> --}}

    {{-- Vehicle Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Vehicle Lookup</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Search by registration number to pre-fill details from an existing quotation</p>
        </div>
        <div class="p-6 space-y-6">

            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Registration Number</label>
                    <div class="relative">
                        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" required
                               placeholder="e.g. KBA 001A"
                               oninput="this.value = this.value.toUpperCase(); fetchSuggestions(this.value)"
                               autocomplete="off"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
                        <div id="suggestionsDropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto"></div>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" id="searchVehicleBtn"
                            class="px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition whitespace-nowrap inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search
                    </button>
                    <button type="button" id="resetVehicleBtn"
                            class="px-4 py-2 text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition whitespace-nowrap">
                        New
                    </button>
                </div>
            </div>

            <div id="vehicleStatus" class="hidden"></div>

            <div id="vehicleFields" class="{{ old('registration_number') ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Make</label>
                        <input type="text" name="vehicle_make" id="vehicle_make" value="{{ old('vehicle_make') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Model</label>
                        <input type="text" name="vehicle_model" id="vehicle_model" value="{{ old('vehicle_model') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Year of Manufacture</label>
                        <input type="number" name="vehicle_year" id="vehicle_year" value="{{ old('vehicle_year') }}" min="1990" max="2030"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <input type="text" name="vehicle_color" id="vehicle_color" value="{{ old('vehicle_color') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Engine Number</label>
                        <input type="text" name="engine_number" id="engine_number" value="{{ old('engine_number') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Chassis Number</label>
                        <input type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Type of Cover</label>
                        <select name="cover_type" id="cover_type" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select cover type...</option>
                            <option value="private" {{ old('cover_type') == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="psv" {{ old('cover_type') == 'psv' ? 'selected' : '' }}>PSV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Insurance Company</label>
                        <input type="text" name="insurance_company" id="insurance_company" value="{{ old('insurance_company') }}" readonly
                               placeholder="Auto-filled from quotation"
                               class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Valuation (KES)</label>
                        <input type="number" name="vehicle_valuation" id="vehicle_valuation" value="{{ old('vehicle_valuation') }}" min="0" step="0.01"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <input type="hidden" name="insurance_company_id" id="insurance_company_id" value="{{ old('insurance_company_id') }}">
            </div>
        </div>
    </div>

    {{-- Incident Details --}}
    <div id="underwriterSection" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Select Insurance Company</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Choose the underwriter to send this claim to</p>
        </div>
        <div class="p-6">
            <div id="underwriterCards" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3"></div>
            <input type="hidden" name="selected_underwriter_id" id="selected_underwriter_id" value="{{ old('selected_underwriter_id') }}">
            <input type="hidden" name="selected_underwriter_email" id="selected_underwriter_email" value="{{ old('selected_underwriter_email') }}">
            <p id="underwriterError" class="hidden mt-2 text-[10px] text-red-600 dark:text-red-400 font-medium">Please select an insurance company to proceed.</p>
        </div>
    </div>

    {{-- Incident Details --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Incident Details</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Describe what happened</p>
        </div>
        <div class="p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Incident Date *</label>
                    <input type="date" name="incident_date" value="{{ old('incident_date') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Have you notified the police? *</label>
               <div class="flex items-center gap-6 mt-1" x-data="{ notified: {{ old('police_notified') !== null ? old('police_notified') : 'null' }} }">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                            :checked="notified === 1"
                            @change="notified = 1"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition">
                        <span class="text-xs text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition">Yes, I have notified the police</span>
                    </label>

                    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox"
                            :checked="notified === 0"
                            @change="notified = 0"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition">
                        <span class="text-xs text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition">No, I haven't notified the police</span>
                    </label>

                    <input type="hidden" name="police_notified" :value="notified">
                </div>
            </div>



            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Incident Statement *</label>
                <textarea name="incident_description" rows="4" required
                          placeholder="Describe the incident in detail..."
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('incident_description') }}</textarea>
            </div>

        </div>
    </div>

    {{-- Document Confirmation --}}
    {{-- <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6"> --}}
        {{-- <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white">Document Confirmation</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Confirm which documents have been submitted</p>
                </div>
                <button type="button" id="selectAllDocs"
                        class="px-3 py-1.5 text-[11px] font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-lg transition">
                    Select All
                </button>
            </div>
        </div> --}}
        {{-- <div class="p-6 space-y-4"> --}}
            {{-- <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach(\App\Models\Claim::documentTypes() as $value => $label)
                    <label class="inline-flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="documents_confirmed[]" value="{{ $value }}" data-doc-check
                               {{ in_array($value, old('documents_confirmed', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition">
                        <span class="text-xs text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition">{{ $label }}</span>
                    </label>
                @endforeach
            </div> --}}

            {{-- Dynamic Other Document Names --}}
            {{-- <div id="otherDocsSection" class="hidden border-t border-gray-100 dark:border-gray-700 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Other Document Names</label>
                    <button type="button" id="addOtherDoc"
                            class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition inline-flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Document
                    </button>
                </div>
                <div id="otherDocsList" class="space-y-2">
                    @if(old('other_documents'))
                        @foreach(old('other_documents') as $idx => $od)
                            @if($od)
                                <div class="flex items-center gap-2 other-doc-row">
                                    <input type="text" name="other_documents[]" value="{{ $od }}"
                                           placeholder="Enter document name..."
                                           class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" class="removeOtherDoc p-2 text-gray-400 hover:text-red-500 transition" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    {{-- </div> --}}

    {{-- Submit --}}
    <div class="flex items-center gap-3 pt-6">
        <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Log Claim
        </button>
        <a href="{{ route('claims.index') }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
            Cancel
        </a>
    </div>

    </form>
</div>

<script>
var SELF_DATA = @json($self);

function setCustomerRequired(required) {
    document.querySelectorAll('#customer-required, #customer-email-required, #customer-phone-required')
        .forEach(function(el) { el.style.display = required ? '' : 'none'; });
}

function fillCustomer(name, email, phone) {
    document.getElementById('customer_name').value = name || '';
    document.getElementById('customer_email').value = email || '';
    document.getElementById('customer_phone').value = phone || '';
}

function getSelectedUserData() {
    var claimFor = document.getElementById('claim_for').value;
    var select = claimFor === 'admin' ? document.getElementById('on_behalf_of_admin') : document.getElementById('on_behalf_of');
    var opt = select ? select.options[select.selectedIndex] : null;
    if (opt && opt.dataset && opt.dataset.name) {
        return { name: opt.dataset.name, email: opt.dataset.email, phone: opt.dataset.phone };
    }
    return null;
}

function updateClaimForUI() {
    var claimFor = document.getElementById('claim_for').value;
    var userSelect = document.getElementById('user-select');
    var adminSelect = document.getElementById('admin-select');

    userSelect.classList.add('hidden');
    adminSelect.classList.add('hidden');

    if (claimFor === 'user') userSelect.classList.remove('hidden');
    if (claimFor === 'admin') adminSelect.classList.remove('hidden');

    if (claimFor === 'customer') {
        setCustomerRequired(true);
    } else {
        setCustomerRequired(false);
        if (claimFor === 'self') {
            fillCustomer(SELF_DATA.name, SELF_DATA.email, SELF_DATA.phone);
        } else {
            var data = getSelectedUserData();
            if (data) fillCustomer(data.name, data.email, data.phone);
        }
    }
}

document.getElementById('claim_for')?.addEventListener('change', updateClaimForUI);
document.getElementById('on_behalf_of')?.addEventListener('change', function() {
    if (document.getElementById('claim_for').value === 'user') {
        var data = getSelectedUserData();
        if (data) fillCustomer(data.name, data.email, data.phone);
    }
});
document.getElementById('on_behalf_of_admin')?.addEventListener('change', function() {
    if (document.getElementById('claim_for').value === 'admin') {
        var data = getSelectedUserData();
        if (data) fillCustomer(data.name, data.email, data.phone);
    }
});

updateClaimForUI();

// --- Vehicle Lookup ---
var searchBtn = document.getElementById('searchVehicleBtn');
var resetBtn = document.getElementById('resetVehicleBtn');
var statusEl = document.getElementById('vehicleStatus');
var vehicleFields = document.getElementById('vehicleFields');
var suggestionsDropdown = document.getElementById('suggestionsDropdown');
var searchTimeout = null;

function fetchSuggestions(query) {
    clearTimeout(suggestionsDropdown);
    if (query.length < 2) {
        suggestionsDropdown.classList.add('hidden');
        suggestionsDropdown.innerHTML = '';
        return;
    }
    searchTimeout = setTimeout(function() {
        fetch('{{ route("claims.suggest-vehicles") }}?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.suggestions && data.suggestions.length > 0) {
                suggestionsDropdown.innerHTML = data.suggestions.map(function(s) {
                    var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                    var regHighlighted = s.registration.replace(regex, '<span class="font-bold text-blue-600 dark:text-blue-400">$1</span>');
                    return '<div class="px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 text-xs border-b border-gray-100 dark:border-gray-700 last:border-0" onclick="selectSuggestion(\'' + s.registration.replace(/'/g, "\\'") + '\', \'' + (s.make || '').replace(/'/g, "\\'") + '\', \'' + (s.model || '').replace(/'/g, "\\'") + '\', ' + (s.year || 'null') + ', \'' + (s.type || '') + '\')">' +
                        '<div class="flex items-center justify-between">' +
                        '<span class="font-medium text-gray-800 dark:text-white">' + regHighlighted + '</span>' +
                        '<span class="text-[9px] px-1.5 py-0.5 rounded-full ' + (s.type === 'psv' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') + '">' + (s.type === 'psv' ? 'PSV' : 'Private') + '</span>' +
                        '</div>' +
                        '<p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">' + (s.make || '') + ' ' + (s.model || '') + ' (' + (s.year || '') + ')</p>' +
                        '</div>';
                }).join('');
                suggestionsDropdown.classList.remove('hidden');
            } else {
                suggestionsDropdown.classList.add('hidden');
            }
        })
        .catch(function() { suggestionsDropdown.classList.add('hidden'); });
    }, 300);
}

function selectSuggestion(reg, make, model, year, type) {
    document.getElementById('registration_number').value = reg;
    suggestionsDropdown.classList.add('hidden');
    searchVehicle(reg);
}

document.getElementById('registration_number').addEventListener('blur', function() {
    setTimeout(function() { suggestionsDropdown.classList.add('hidden'); }, 200);
});

document.getElementById('registration_number').addEventListener('focus', function() {
    if (this.value.length >= 2) fetchSuggestions(this.value);
});

searchBtn.addEventListener('click', function() {
    searchVehicle(document.getElementById('registration_number').value.trim());
});

function searchVehicle(reg) {
    if (!reg) {
        statusEl.className = 'rounded-lg p-3 text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700';
        statusEl.textContent = 'Please enter a registration number to search.';
        statusEl.classList.remove('hidden');
        return;
    }

    searchBtn.disabled = true;
    searchBtn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Searching...';

    fetch('{{ route("claims.lookup-vehicle") }}?registration_number=' + encodeURIComponent(reg), {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function(data) {
        if (data.found) {
            statusEl.className = 'rounded-lg p-3 text-xs font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700';
            statusEl.innerHTML = '<span class="font-semibold">Vehicle found!</span> Customer and vehicle details pre-filled from quotation.';
            statusEl.classList.remove('hidden');

            fillCustomer(data.customer_name, data.customer_email, data.customer_phone);

            document.getElementById('vehicle_make').value = data.vehicle.make || '';
            document.getElementById('vehicle_model').value = data.vehicle.model || '';
            document.getElementById('vehicle_year').value = data.vehicle.year || '';
            document.getElementById('vehicle_valuation').value = data.vehicle.valuation || '';
            document.getElementById('cover_type').value = data.vehicle.cover_type || '';
            document.getElementById('insurance_company').value = data.company_name || '';
            document.getElementById('insurance_company_id').value = data.company_id || '';

            vehicleFields.classList.remove('hidden');
            showUnderwriterCards(data.company_id, data.company_name);
        } else {
            statusEl.className = 'rounded-lg p-3 text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-700';
            statusEl.innerHTML = '<span class="font-semibold">No vehicle found.</span> Please fill in the vehicle details manually.';
            statusEl.classList.remove('hidden');
            vehicleFields.classList.remove('hidden');
        }
    })
    .catch(function(err) {
        console.error('Vehicle search error:', err);
        statusEl.className = 'rounded-lg p-3 text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700';
        statusEl.textContent = 'Search failed. Please try again.';
        statusEl.classList.remove('hidden');
    })
    .finally(function() {
        searchBtn.disabled = false;
        searchBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Search';
    });
}

var ALL_COMPANIES = @json($companies->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug, 'logo' => $c->logoUrl(), 'claims_email' => $c->claims_email]));

var COMPANY_LOGO_MAP = {
    'GA': '{{ asset("images/GA Insurance.png") }}',
    'SANLAM': '{{ asset("images/Sanlam-Allianz.png") }}',
    'DEFINITE': '{{ asset("images/definite.png") }}',
    'BRITAM': '{{ asset("images/britam.png") }}',
};

function getCompanyLogo(company) {
    if (company.logo) return company.logo;
    if (COMPANY_LOGO_MAP[company.slug]) return COMPANY_LOGO_MAP[company.slug];
    var initials = company.name.substring(0, 2).toUpperCase();
    return 'data:image/svg+xml,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"><rect width="40" height="40" rx="8" fill="%23005F9E"/><text x="50%" y="55%" text-anchor="middle" fill="white" font-family="Arial,sans-serif" font-size="14" font-weight="bold" dy=".35em">' + initials + '</text></svg>');
}

function showUnderwriterCards(preselectedId, preselectedName) {
    var section = document.getElementById('underwriterSection');
    var cardsEl = document.getElementById('underwriterCards');
    var hiddenId = document.getElementById('selected_underwriter_id');
    var hiddenEmail = document.getElementById('selected_underwriter_email');
    var errEl = document.getElementById('underwriterError');

    cardsEl.innerHTML = '';
    section.classList.remove('hidden');
    errEl.classList.add('hidden');

    ALL_COMPANIES.forEach(function(c) {
        var isSelected = (preselectedId && c.id == preselectedId) || (!preselectedId && preselectedName && c.name === preselectedName);
        var card = document.createElement('div');
        card.className = 'relative border-2 rounded-xl p-3 cursor-pointer transition-all duration-200 text-center hover:shadow-md' +
            (isSelected ? ' border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-md ring-2 ring-blue-200 dark:ring-blue-800' : ' border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 bg-white dark:bg-gray-750');
        card.setAttribute('data-company-id', c.id);
        card.onclick = function() { selectUnderwriter(c.id, c.name); };

        card.innerHTML =
            '<div class="w-10 h-10 mx-auto mb-2 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">' +
            '<img src="' + getCompanyLogo(c) + '" alt="' + c.name + '" class="w-full h-full object-contain" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">' +
            '<div class="w-full h-full flex items-center justify-center text-[10px] font-bold text-white bg-blue-600 rounded-lg" style="display:none">' + c.name.substring(0, 2).toUpperCase() + '</div>' +
            '</div>' +
            '<p class="text-[11px] font-semibold text-gray-800 dark:text-white truncate">' + c.name + '</p>' +
            '<div class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all ' +
            (isSelected ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-gray-500') + '">' +
            (isSelected ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : '') +
            '</div>';

        cardsEl.appendChild(card);
    });

    if (preselectedId) {
        selectUnderwriter(preselectedId, preselectedName);
    }
}

function selectUnderwriter(companyId, companyName) {
    var hiddenId = document.getElementById('selected_underwriter_id');
    var hiddenEmail = document.getElementById('selected_underwriter_email');
    var errEl = document.getElementById('underwriterError');

    hiddenId.value = companyId;
    errEl.classList.add('hidden');

    var company = ALL_COMPANIES.find(function(c) { return c.id == companyId; });
    hiddenEmail.value = company ? (company.claims_email || '') : '';

    document.querySelectorAll('#underwriterCards > div').forEach(function(card) {
        var cardId = card.getAttribute('data-company-id');
        var isSelected = cardId == companyId;
        card.className = card.className.replace(/border-blue-500|border-gray-200|border-blue-300|bg-blue-50|bg-white|shadow-md|ring-2|ring-blue-200/g, '').trim();
        card.className += isSelected ?
            ' border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-md ring-2 ring-blue-200 dark:ring-blue-800' :
            ' border-gray-200 dark:border-gray-600 hover:border-blue-300 bg-white dark:bg-gray-750';
    });
}

resetBtn.addEventListener('click', function() {
    document.getElementById('registration_number').value = '';
    document.getElementById('vehicle_make').value = '';
    document.getElementById('vehicle_model').value = '';
    document.getElementById('vehicle_year').value = '';
    document.getElementById('vehicle_color').value = '';
    document.getElementById('engine_number').value = '';
    document.getElementById('chassis_number').value = '';
    document.getElementById('cover_type').value = '';
    document.getElementById('insurance_company').value = '';
    document.getElementById('insurance_company_id').value = '';
    document.getElementById('vehicle_valuation').value = '';
    document.getElementById('selected_underwriter_id').value = '';
    document.getElementById('selected_underwriter_email').value = '';
    document.getElementById('underwriterSection').classList.add('hidden');
    document.getElementById('underwriterError').classList.add('hidden');
    statusEl.classList.add('hidden');
    vehicleFields.classList.add('hidden');
});

// --- Select All Documents ---
document.getElementById('selectAllDocs').addEventListener('click', function() {
    var checkboxes = document.querySelectorAll('[data-doc-check]');
    var allChecked = Array.from(checkboxes).every(function(cb) { return cb.checked; });
    checkboxes.forEach(function(cb) { cb.checked = !allChecked; });
    updateOtherDocsSection();
});

function updateOtherDocsSection() {
    var otherChecked = document.querySelector('[data-doc-check][value="other"]');
    var section = document.getElementById('otherDocsSection');
    if (otherChecked && otherChecked.checked) {
        section.classList.remove('hidden');
    } else {
        section.classList.add('hidden');
    }
}

document.querySelectorAll('[data-doc-check]').forEach(function(cb) {
    cb.addEventListener('change', updateOtherDocsSection);
});

// --- Dynamic Other Documents ---
var addBtn = document.getElementById('addOtherDoc');
var docsList = document.getElementById('otherDocsList');

function createOtherDocRow(value) {
    var div = document.createElement('div');
    div.className = 'flex items-center gap-2 other-doc-row';
    div.innerHTML = '<input type="text" name="other_documents[]" value="' + (value || '') + '" placeholder="Enter document name..." ' +
        'class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">' +
        '<button type="button" class="removeOtherDoc p-2 text-gray-400 hover:text-red-500 transition" title="Remove">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
    div.querySelector('.removeOtherDoc').addEventListener('click', function() { div.remove(); });
    docsList.appendChild(div);
}

addBtn.addEventListener('click', function() { createOtherDocRow(''); });

docsList.addEventListener('click', function(e) {
    if (e.target.closest('.removeOtherDoc')) {
        e.target.closest('.other-doc-row').remove();
    }
});

// Pre-fill old values
@if(old('other_documents') && count(old('other_documents')) > 0)
    updateOtherDocsSection();
@endif
</script>
</x-app-layout>
