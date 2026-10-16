<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Claims</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Claim Tracker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Track the status of your insurance claims by registration number</p>
            </div>
            @auth
                <a href="{{ route('claims.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    My Claims
                </a>
            @endauth
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <form method="GET" action="{{ route('claims.public-tracker') }}" class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex-1 w-full">
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Vehicle Registration Number</label>
                    <input type="text" name="registration_number" value="{{ $registrationNumber }}" placeholder="e.g. KDH 724V" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Track Claim
                </button>
            </form>
        </div>

        @if($searched)
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Results</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $claims->count() }} claim(s) found</p>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($claims as $claim)
                    <div class="px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-blue-600 hover:text-blue-800 text-xs">{{ $claim->underwriter_claim_number ?: $claim->registration_number }}</span>
                                    <span class="text-gray-400 text-[10px]">{{ $claim->registration_number }}</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    @if($claim->onBehalfOfUser)
                                        On behalf of {{ $claim->onBehalfOfUser->username }} &middot; {{ $claim->vehicle_description }}
                                    @else
                                        {{ $claim->vehicle_description }}
                                    @endif
                                    &middot; {{ $claim->incident_date ? \Carbon\Carbon::parse($claim->incident_date)->format('d M Y') : 'No date' }}
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    @php
                                        $statusColor = match($claim->status) {
                                            \App\Models\Claim::STATUS_REPORTED => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400',
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
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <p class="text-xs text-gray-400">No claims found for this registration number.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
</x-app-layout>
