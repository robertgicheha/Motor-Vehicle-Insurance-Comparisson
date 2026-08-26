<x-app-layout>
<div class="max-w-5xl mx-auto space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Claim Management</p>
                <div class="flex items-center gap-3 mt-0.5">
                    <h1 class="text-2xl font-bold text-white tracking-tight" style="font-size:26px">{{ $claim->claim_number }}</h1>
                    @php
                        $statusColor = match($claim->status) {
                            \App\Models\Claim::STATUS_REPORTED => 'bg-gray-500/20 text-gray-300',
                            \App\Models\Claim::STATUS_DOCUMENTS_PENDING => 'bg-amber-500/20 text-amber-300',
                            \App\Models\Claim::STATUS_UNDER_REVIEW => 'bg-blue-500/20 text-blue-300',
                            \App\Models\Claim::STATUS_SETTLEMENT => 'bg-purple-500/20 text-purple-300',
                            \App\Models\Claim::STATUS_UNDER_INVESTIGATION => 'bg-orange-500/20 text-orange-300',
                            \App\Models\Claim::STATUS_SETTLED => 'bg-emerald-500/20 text-emerald-300',
                            \App\Models\Claim::STATUS_DECLINED => 'bg-red-500/20 text-red-300',
                            default => 'bg-gray-500/20 text-gray-300',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColor }}">{{ \App\Models\Claim::statuses()[$claim->status] ?? ucfirst($claim->status) }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-400">{{ $claim->registration_number }} &middot; Logged {{ $claim->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.claims.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold border border-white/20 text-white/80 hover:bg-white/10 rounded-lg transition">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Customer</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Customer Details</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6 text-xs">
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Name</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->customer_name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->customer_email ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Phone</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->customer_phone ?? 'N/A' }}</span>
                    </div>
                    @if($claim->onBehalfOfUser)
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">On Behalf Of</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->onBehalfOfUser->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Vehicle</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Vehicle Details</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 p-6 text-xs">
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Make</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['make'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Model</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['model'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Year</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['year'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Color</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['color'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Engine No</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['engine_number'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Chassis No</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['chassis_number'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Cover Type</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ ucfirst($claim->vehicle_details['cover_type'] ?? 'N/A') }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Valuation</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->vehicle_details['valuation'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Incident</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Incident Details</h2>
                </div>
                <div class="p-6 text-xs space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Date</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->incident_date ? $claim->incident_date->format('d M Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Police Ref</span>
                            <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $claim->police_abstract_reference ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Description</span>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $claim->incident_description ?? 'No description provided.' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Documents</p>
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white mt-0.5">Document Confirmation</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $confirmed = $claim->documents_confirmed ?? [];
                        @endphp
                        @foreach(\App\Models\Claim::documentTypes() as $value => $label)
                            <div class="flex items-center gap-2">
                                @if(in_array($value, $confirmed))
                                    <div class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="text-xs text-gray-700 dark:text-gray-300 line-through opacity-75">{{ $label }}</span>
                                @else
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-600 shrink-0"></div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Status Timeline</p>
                <div class="mt-4 space-y-4">
                    @php
                        $currentStatus = $claim->status;
                        $statusOrder = [
                            \App\Models\Claim::STATUS_REPORTED,
                            \App\Models\Claim::STATUS_DOCUMENTS_PENDING,
                            \App\Models\Claim::STATUS_UNDER_REVIEW,
                            \App\Models\Claim::STATUS_SETTLEMENT,
                            \App\Models\Claim::STATUS_UNDER_INVESTIGATION,
                            \App\Models\Claim::STATUS_SETTLED,
                            \App\Models\Claim::STATUS_DECLINED,
                        ];
                        $statusLabels = [
                            \App\Models\Claim::STATUS_REPORTED => 'Claim is Reported',
                            \App\Models\Claim::STATUS_DOCUMENTS_PENDING => 'Documents - Pending',
                            \App\Models\Claim::STATUS_UNDER_REVIEW => 'Under Review',
                            \App\Models\Claim::STATUS_SETTLEMENT => 'Settlement - Awaiting Offer',
                            \App\Models\Claim::STATUS_UNDER_INVESTIGATION => 'Under Investigation',
                            \App\Models\Claim::STATUS_SETTLED => 'Settlement - Settled',
                            \App\Models\Claim::STATUS_DECLINED => 'Declined',
                        ];
                        $statusIcons = [
                            \App\Models\Claim::STATUS_REPORTED => '📢',
                            \App\Models\Claim::STATUS_DOCUMENTS_PENDING => '📁',
                            \App\Models\Claim::STATUS_UNDER_REVIEW => '📋',
                            \App\Models\Claim::STATUS_SETTLEMENT => '💬',
                            \App\Models\Claim::STATUS_UNDER_INVESTIGATION => '🔍',
                            \App\Models\Claim::STATUS_SETTLED => '✅',
                            \App\Models\Claim::STATUS_DECLINED => '❌',
                        ];
                        $currentIndex = array_search($currentStatus, $statusOrder);
                    @endphp

                    @foreach($statusOrder as $index => $status)
                        @php
                            $isCompleted = $currentIndex > $index || in_array($currentStatus, [\App\Models\Claim::STATUS_SETTLED, \App\Models\Claim::STATUS_DECLINED]);
                            $isCurrent = $currentStatus === $status;
                            $isSkipped = $currentStatus === \App\Models\Claim::STATUS_DECLINED && in_array($status, [\App\Models\Claim::STATUS_SETTLEMENT, \App\Models\Claim::STATUS_UNDER_INVESTIGATION, \App\Models\Claim::STATUS_UNDER_REVIEW, \App\Models\Claim::STATUS_DOCUMENTS_PENDING]);
                            $isSkipped = $currentStatus === \App\Models\Claim::STATUS_SETTLED && in_array($status, [\App\Models\Claim::STATUS_UNDER_INVESTIGATION, \App\Models\Claim::STATUS_DOCUMENTS_PENDING, \App\Models\Claim::STATUS_REPORTED]);
                        @endphp
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                @if($isCurrent) bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 ring-2 ring-blue-500
                                @elseif($isCompleted) bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                                @elseif($isSkipped) bg-gray-100 dark:bg-gray-700 text-gray-400 opacity-50
                                @else bg-gray-100 dark:bg-gray-700 text-gray-400
                                @endif">
                                @if($isCompleted && !$isCurrent)
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @else
                                    {{ $statusIcons[$status] }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-medium
                                    @if($isCurrent) text-blue-600 dark:text-blue-400 font-semibold
                                    @elseif($isCompleted) text-gray-800 dark:text-white
                                    @elseif($isSkipped) text-gray-400 line-through opacity-50
                                    @else text-gray-400
                                    @endif">
                                    {{ $statusLabels[$status] }}
                                </p>
                                @if($isCurrent)
                                    <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-0.5">Current status</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($claim->vehicle_assessment_done)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Assessment</p>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Vehicle assessment completed by claimant.</p>
                    </div>
                </div>
            @endif

            @if($claim->settlement_amount)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Settlement</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-2">KES {{ number_format($claim->settlement_amount, 2) }}</p>
                    @if($claim->settlement_notes)
                        <p class="text-xs text-gray-500 mt-1">{{ $claim->settlement_notes }}</p>
                    @endif
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Admin Actions</p>
                <form method="POST" action="{{ route('admin.claims.update-status', $claim) }}" class="mt-3 space-y-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Update Status</label>
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach(\App\Models\Claim::statuses() as $value => $label)
                                <option value="{{ $value }}" {{ $claim->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Settlement Amount (KES)</label>
                        <input type="number" name="settlement_amount" value="{{ old('settlement_amount', $claim->settlement_amount) }}" step="0.01"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Settlement Notes</label>
                        <textarea name="settlement_notes" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('settlement_notes', $claim->settlement_notes) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Admin Notes</label>
                        <textarea name="admin_notes" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('admin_notes', $claim->admin_notes) }}</textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                        Update Claim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
