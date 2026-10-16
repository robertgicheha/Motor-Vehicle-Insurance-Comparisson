<x-app-layout>
<div class="max-w-7xl mx-auto space-y-6 pt-10 fade-in" x-data="claimForm()">
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
    <input type="hidden" name="upload_token" value="{{ $uploadToken }}">

    {{-- Vehicle Lookup --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Vehicle Lookup</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">Search by registration number to pre-fill details from an existing quotation</p>
        </div>
        <div class="p-6 space-y-6">

            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1" for="registration_number">Registration Number</label>
                    <div class="relative">
                        <input type="text" name="registration_number" id="registration_number" x-model="registrationNumber" value="{{ old('registration_number') }}" required
                               placeholder="e.g. KBA 001A"
                               @input="registrationNumber = registrationNumber.toUpperCase(); onRegInput()"
                               @blur="setTimeout(() => suggestions = [], 200)"
                               @focus="if (registrationNumber.length >= 3) fetchSuggestions()"
                               autocomplete="off"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase"
                               :readonly="vehicleFound">
                        <div x-show="suggestions.length > 0" x-cloak class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="s in suggestions" :key="s.registration">
                                <div @click="selectSuggestion(s)" class="px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 text-xs border-b border-gray-100 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-800 dark:text-white" x-text="s.registration"></span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded-full" :class="s.type === 'psv' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'" x-text="s.type === 'psv' ? 'PSV' : 'Private'"></span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5" x-text="(s.make || '') + ' ' + (s.model || '') + ' (' + (s.year || '') + ')'"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" @click="searchVehicle()" :disabled="searching"
                            class="px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 disabled:opacity-50 text-white rounded-lg transition whitespace-nowrap inline-flex items-center gap-1.5">
                        <svg x-show="!searching" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <svg x-show="searching" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="searching ? 'Searching...' : 'Search'"></span>
                    </button>
                    <button type="button" @click="resetVehicle()"
                            class="px-4 py-2 text-xs font-semibold border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition whitespace-nowrap">
                        New
                    </button>
                </div>
            </div>

            <div x-show="statusMessage" x-transition x-cloak class="rounded-lg p-3 text-xs font-medium border"
                 :class="statusType === 'success' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700' : (statusType === 'error' ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-700' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700')">
                <span x-html="statusMessage"></span>
            </div>

            <div x-show="vehicleSearched" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Make</label>
                    <input type="text" name="vehicle_make" x-model="vehicle.make" :readonly="vehicleFound"
                           :class="vehicleFound ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white'"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Model</label>
                    <input type="text" name="vehicle_model" x-model="vehicle.model" :readonly="vehicleFound"
                           :class="vehicleFound ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white'"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Year of Manufacture</label>
                    <input type="number" name="vehicle_year" x-model="vehicle.year" min="1990" max="2030" :readonly="vehicleFound"
                           :class="vehicleFound ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white'"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Insurance Company: readonly text once a quotation match resolves it;
                     a real <select> when there's no match, since we still need to know
                     which underwriter to notify. --}}
                <div x-show="vehicleFound">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Underwriter</label>
                    <input type="text" x-model="vehicle.company_name" readonly
                           class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                </div>
                <div x-show="!vehicleFound">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Underwriter</label>
                    <select name="insurance_company_id" x-model="vehicle.company_id"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select insurer...</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Submitted alongside the select above when a match was found, since
                     the visible field there is a plain readonly text input. --}}
                <input type="hidden" name="insurance_company_id" x-show="vehicleFound" x-bind:value="vehicle.company_id" x-cloak>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Vehicle Valuation (KES)</label>
                    <input type="number" name="vehicle_valuation" x-model="vehicle.valuation" min="0" step="0.01" :readonly="vehicleFound"
                           :class="vehicleFound ? 'bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white'"
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Captured from the matched quotation but not shown — not part of
                     the six fields the claim form should surface, kept only so the
                     record has them if available. --}}
                <input type="hidden" name="vehicle_color" x-bind:value="vehicle.color">
                <input type="hidden" name="engine_number" x-bind:value="vehicle.engine_number">
                <input type="hidden" name="chassis_number" x-bind:value="vehicle.chassis_number">
                <input type="hidden" name="cover_type" x-bind:value="vehicle.cover_type">
            </div>
        </div>
    </div>

    {{-- Claim Type --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mt-6">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-bold text-gray-800 dark:text-white">Type of Claim *</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">What kind of claim is this?</p>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach(\App\Models\Claim::claimTypes() as $value => $label)
                    <label class="relative flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                        :class="claimType === '{{ $value }}' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-blue-300'">
                        <input type="radio" name="claim_type" value="{{ $value }}" x-model="claimType" {{ old('claim_type') == $value ? 'checked' : '' }} required
                               class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            <div x-show="claimType === 'others'" x-transition x-cloak>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1" for="claim_type_other">Please explain *</label>
                <textarea name="claim_type_other" id="claim_type_other" rows="2" x-bind:required="claimType === 'others'"
                          placeholder="Briefly describe the type of claim..."
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('claim_type_other') }}</textarea>
            </div>
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
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1" for="incident_date">Incident Date *</label>
                    <input type="date" name="incident_date" id="incident_date" value="{{ old('incident_date') }}" required
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
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1" for="incident_description">Incident Statement *</label>
                <textarea name="incident_description" id="incident_description" rows="4" required
                          placeholder="Describe the incident in detail..."
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('incident_description') }}</textarea>
            </div>

            {{-- Police Abstract — a single optional slot --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Police Abstract</label>
                <p class="text-[10px] text-gray-400 mb-2">Attach if available — not required</p>

                <div x-show="!policeAbstract.uploading && !policeAbstract.documentId" class="max-w-sm">
                    <input type="file" id="police-abstract-input" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadPoliceAbstract($event)">
                    <label for="police-abstract-input"
                       class="flex flex-col items-center justify-center w-full py-5 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all duration-200">
                        <svg class="w-7 h-7 text-gray-300 dark:text-gray-500 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="text-[10px] font-medium text-gray-500 dark:text-gray-400">Drop or click</span>
                        <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-0.5">PDF, JPG or JPEG up to 5MB</span>
                    </label>
                </div>
                <div x-show="policeAbstract.uploading" class="max-w-sm">
                    <div class="flex items-center gap-2 p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="animate-spin w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span class="text-[10px] font-medium text-blue-600 dark:text-blue-400">Uploading...</span>
                    </div>
                </div>
                <div x-show="!policeAbstract.uploading && policeAbstract.documentId" class="max-w-sm">
                    <div class="flex items-center gap-2 p-2.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 truncate" x-text="policeAbstract.filename"></p>
                            <p class="text-[9px] text-emerald-600 dark:text-emerald-500">Attached</p>
                        </div>
                        <button type="button" @click="removePoliceAbstract()" class="text-emerald-600 hover:text-red-500 dark:text-emerald-400 dark:hover:text-red-400 transition-colors p-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Incident Photos — any number of independent rows, each uploaded on
                 its own so a partial batch is never ambiguous. --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-5">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Incident Photos</label>
                        <p class="text-[10px] text-gray-400">Attach if available — not required</p>
                    </div>
                    <button type="button" @click="addPhotoRow()"
                        class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Photo
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="row in photoRows" :key="row.rowId">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-750 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 w-16 shrink-0">Photo <span x-text="photoRows.indexOf(row) + 1"></span></span>

                            <template x-if="!row.documentId">
                                <span class="flex-1 min-w-0">
                                    <input type="file" :id="'photo-' + row.rowId" accept=".pdf,.jpg,.jpeg" class="hidden" @change="uploadPhoto($event, row)">
                                    <label :for="'photo-' + row.rowId" x-show="!row.uploading"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg cursor-pointer transition whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        Upload
                                    </label>
                                    <span x-show="row.uploading" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[10px] font-semibold text-blue-600">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Uploading...
                                    </span>
                                </span>
                            </template>

                            <span x-show="row.documentId" class="flex-1 min-w-0 inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="row.filename" class="truncate"></span>
                            </span>

                            <button type="button" @click="removePhotoRow(row)" class="text-gray-400 hover:text-red-500 transition p-1 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>

                    <p x-show="photoRows.length === 0" class="text-[10px] text-gray-400 text-center py-2">No photos added yet.</p>
                </div>
            </div>

        </div>
    </div>

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

@push('scripts')
<script>
function claimForm() {
    return {
        uploadToken: {{ \Illuminate\Support\Js::from($uploadToken) }},

        // --- Vehicle lookup ---
        registrationNumber: {{ \Illuminate\Support\Js::from(old('registration_number', '')) }},
        searching: false,
        searchTimeout: null,
        suggestions: [],
        vehicleSearched: {{ old('registration_number') ? 'true' : 'false' }},
        vehicleFound: false,
        statusMessage: '',
        statusType: 'info',
        vehicle: {
            make: {{ \Illuminate\Support\Js::from(old('vehicle_make', '')) }},
            model: {{ \Illuminate\Support\Js::from(old('vehicle_model', '')) }},
            year: {{ \Illuminate\Support\Js::from(old('vehicle_year', '')) }},
            color: {{ \Illuminate\Support\Js::from(old('vehicle_color', '')) }},
            engine_number: {{ \Illuminate\Support\Js::from(old('engine_number', '')) }},
            chassis_number: {{ \Illuminate\Support\Js::from(old('chassis_number', '')) }},
            cover_type: {{ \Illuminate\Support\Js::from(old('cover_type', '')) }},
            valuation: {{ \Illuminate\Support\Js::from(old('vehicle_valuation', '')) }},
            company_id: {{ \Illuminate\Support\Js::from(old('insurance_company_id', '')) }},
            company_name: '',
        },

        // --- Claim type ---
        claimType: {{ \Illuminate\Support\Js::from(old('claim_type', '')) }},

        // --- Attachments ---
        policeAbstract: { documentId: null, filename: null, uploading: false },
        photoRows: [],
        nextRowId: 1,

        init() {
            const staged = {{ \Illuminate\Support\Js::from($stagedDocuments) }};
            staged.forEach(d => {
                if (d.document_type === 'police_abstract') {
                    this.policeAbstract = { documentId: d.id, filename: d.filename, uploading: false };
                } else if (d.document_type === 'incident_photo') {
                    this.photoRows.push({ rowId: this.nextRowId++, documentId: d.id, filename: d.filename, uploading: false });
                }
            });
        },

        onRegInput() {
            clearTimeout(this.searchTimeout);
            if (this.registrationNumber.length < 3) {
                this.suggestions = [];
                return;
            }
            this.searchTimeout = setTimeout(() => this.fetchSuggestions(), 250);
        },

        async fetchSuggestions() {
            if (this.registrationNumber.length < 3) { this.suggestions = []; return; }
            try {
                const res = await fetch('{{ route("claims.suggest-vehicles") }}?q=' + encodeURIComponent(this.registrationNumber), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                this.suggestions = data.suggestions || [];
            } catch (e) {
                this.suggestions = [];
            }
        },

        selectSuggestion(s) {
            this.registrationNumber = s.registration;
            this.suggestions = [];
            this.searchVehicle();
        },

        async searchVehicle() {
            if (!this.registrationNumber) {
                this.statusType = 'error';
                this.statusMessage = 'Please enter a registration number to search.';
                return;
            }

            this.searching = true;
            this.suggestions = [];

            try {
                const res = await fetch('{{ route("claims.lookup-vehicle") }}?registration_number=' + encodeURIComponent(this.registrationNumber), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                this.vehicleSearched = true;

                if (data.found) {
                    this.vehicleFound = true;
                    this.statusType = 'success';
                    this.statusMessage = '<span class="font-semibold">Vehicle found!</span> Details pre-filled from quotation and locked for editing.';

                    this.vehicle = {
                        make: data.vehicle.make || '',
                        model: data.vehicle.model || '',
                        year: data.vehicle.year || '',
                        color: data.vehicle.color || '',
                        engine_number: data.vehicle.engine_number || '',
                        chassis_number: data.vehicle.chassis_number || '',
                        cover_type: data.vehicle.cover_type || '',
                        valuation: data.vehicle.valuation || '',
                        company_id: data.company_id || '',
                        company_name: data.company_name || '',
                    };
                } else {
                    this.vehicleFound = false;
                    this.statusType = 'warning';
                    this.statusMessage = '<span class="font-semibold">No vehicle found.</span> Please fill in the vehicle details manually and select the insurer.';
                }
            } catch (e) {
                this.statusType = 'error';
                this.statusMessage = 'Search failed. Please try again.';
            } finally {
                this.searching = false;
            }
        },

        resetVehicle() {
            this.registrationNumber = '';
            this.vehicleSearched = false;
            this.vehicleFound = false;
            this.statusMessage = '';
            this.vehicle = { make: '', model: '', year: '', color: '', engine_number: '', chassis_number: '', cover_type: '', valuation: '', company_id: '', company_name: '' };
        },

        // --- Police abstract ---
        async uploadPoliceAbstract(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.policeAbstract.uploading = true;

            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', 'police_abstract');
            formData.append('upload_token', this.uploadToken);

            try {
                const res = await fetch('{{ route("claims.upload-document") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    this.policeAbstract = { documentId: data.document_id, filename: data.filename, uploading: false };
                    if (typeof showToastSuccess === 'function') showToastSuccess('Police abstract attached.');
                } else {
                    this.policeAbstract.uploading = false;
                    if (typeof showToastError === 'function') showToastError(data.error || 'Upload failed.');
                }
            } catch (e) {
                this.policeAbstract.uploading = false;
                if (typeof showToastError === 'function') showToastError('Upload error. Please try again.');
            } finally {
                event.target.value = '';
            }
        },

        async removePoliceAbstract() {
            if (this.policeAbstract.documentId) {
                try {
                    await fetch('{{ route("claims.remove-document") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ document_id: this.policeAbstract.documentId }),
                    });
                } catch (e) { /* removed from the UI regardless */ }
            }
            this.policeAbstract = { documentId: null, filename: null, uploading: false };
        },

        // --- Incident photos ---
        addPhotoRow() {
            this.photoRows.push({ rowId: this.nextRowId++, documentId: null, filename: null, uploading: false });
        },

        async uploadPhoto(event, row) {
            const file = event.target.files[0];
            if (!file) return;
            row.uploading = true;

            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', 'incident_photo');
            formData.append('upload_token', this.uploadToken);

            try {
                const res = await fetch('{{ route("claims.upload-document") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    row.documentId = data.document_id;
                    row.filename = data.filename;
                    if (typeof showToastSuccess === 'function') showToastSuccess('Photo uploaded.');
                } else if (typeof showToastError === 'function') {
                    showToastError(data.error || 'Upload failed.');
                }
            } catch (e) {
                if (typeof showToastError === 'function') showToastError('Upload error. Please try again.');
            } finally {
                row.uploading = false;
                event.target.value = '';
            }
        },

        async removePhotoRow(row) {
            if (row.documentId) {
                try {
                    await fetch('{{ route("claims.remove-document") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ document_id: row.documentId }),
                    });
                } catch (e) { /* row is removed from the UI regardless */ }
            }
            this.photoRows = this.photoRows.filter(r => r.rowId !== row.rowId);
        },
    };
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
            statusEl.innerHTML = '<span class="font-semibold">Vehicle found!</span> Vehicle details pre-filled from quotation.';
            statusEl.classList.remove('hidden');

            document.getElementById('vehicle_make').value = data.vehicle.make || '';
            document.getElementById('vehicle_model').value = data.vehicle.model || '';
            document.getElementById('vehicle_year').value = data.vehicle.year || '';
            document.getElementById('vehicle_color').value = data.vehicle.color || '';
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

var ALL_COMPANIES = @json($companiesForJs);

var COMPANY_LOGO_MAP = {
    'GA': '{{ asset("images/GA Insurance.png") }}',
    'SANLAM': '{{ asset("images/Sanlam-Allianz.png") }}',
    'BRITAM': '{{ asset("images/britam_logo.png") }}',
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
document.getElementById('selectAllDocs')?.addEventListener('click', function() {
    var checkboxes = document.querySelectorAll('[data-doc-check]');
    var allChecked = Array.from(checkboxes).every(function(cb) { return cb.checked; });
    checkboxes.forEach(function(cb) { cb.checked = !allChecked; });
    updateOtherDocsSection();
});

function updateOtherDocsSection() {
    var otherChecked = document.querySelector('[data-doc-check][value="other"]');
    var section = document.getElementById('otherDocsSection');
    if (!section) return;
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
        'class="flex-1 min-w-0 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">' +
        '<button type="button" class="removeOtherDoc p-2 text-gray-400 hover:text-red-500 transition" title="Remove">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
    div.querySelector('.removeOtherDoc').addEventListener('click', function() { div.remove(); });
    docsList.appendChild(div);
}

addBtn?.addEventListener('click', function() { createOtherDocRow(''); });

docsList?.addEventListener('click', function(e) {
    if (e.target.closest('.removeOtherDoc')) {
        e.target.closest('.other-doc-row').remove();
    }
});

// Pre-fill old values
@if(old('other_documents') && count(old('other_documents')) > 0)
    updateOtherDocsSection();
@endif
</script>
@endpush
</x-app-layout>
