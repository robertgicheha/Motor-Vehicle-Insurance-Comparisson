<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Claims Management</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">All Claims</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage and review all insurance claims</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Total</p>
            <p class="text-lg font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Reported</p>
            <p class="text-lg font-bold text-gray-600 dark:text-gray-400 mt-1">{{ $stats['reported'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Documents</p>
            <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $stats['documents_pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Review</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['under_review'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Settlement</p>
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['settlement'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Investigation</p>
            <p class="text-lg font-bold text-orange-600 dark:text-orange-400 mt-1">{{ $stats['under_investigation'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Settled</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['settled'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Claims</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $claims->total() }} total claims</p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" name="claim_number" value="{{ request('claim_number') }}"
                           placeholder="Claim number..."
                           class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <input type="text" name="registration_number" value="{{ request('registration_number') }}"
                           placeholder="Reg number..."
                           class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <select name="status" class="px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\Claim::statuses() as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($claims as $claim)
                <div class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.claims.show', $claim) }}" class="font-medium text-blue-600 hover:text-blue-800 text-xs">{{ $claim->claim_number }}</a>
                                <span class="text-gray-400 text-[10px]">{{ $claim->registration_number }}</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                @if($claim->onBehalfOfUser)
                                    On behalf of {{ $claim->onBehalfOfUser->name }}
                                @else
                                    {{ $claim->customer_name ?? $claim->user->name }}
                                @endif
                                &middot; {{ $claim->incident_date ? $claim->incident_date->format('d M Y') : 'No date' }}
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                @php
                                    $statusColor = match($claim->status) {
                                        \App\Models\Claim::STATUS_REPORTED => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                        \App\Models\Claim::STATUS_DOCUMENTS_PENDING => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        \App\Models\Claim::STATUS_UNDER_REVIEW => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        \App\Models\Claim::STATUS_SETTLEMENT => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                        \App\Models\Claim::STATUS_UNDER_INVESTIGATION => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        \App\Models\Claim::STATUS_SETTLED => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        \App\Models\Claim::STATUS_DECLINED => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusColor }}">
                                    {{ \App\Models\Claim::statuses()[$claim->status] ?? ucfirst($claim->status) }}
                                </span>
                                @if($claim->vehicle_assessment_done)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        Assessment Done
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[10px] text-gray-400">{{ $claim->created_at->format('d M Y, H:i') }}</p>
                            <a href="{{ route('admin.claims.show', $claim) }}" class="inline-flex items-center gap-1 mt-1 text-[10px] font-medium text-blue-600 hover:text-blue-800">
                                Manage
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-xs text-gray-400">No claims yet.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($claims->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $claims->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
