<x-app-layout>
<div class="space-y-6 pt-10 fade-in max-w-4xl mx-auto">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Change Request #{{ $changeRequest->id }}</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">
                    {{ ucfirst($changeRequest->action) }} {{ ucfirst($changeRequest->type) }}
                </h1>
                <p class="mt-0.5 text-xs text-slate-400">
                    Requested by {{ $changeRequest->user->name }} &middot; {{ $changeRequest->created_at->format('d M Y H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.change-requests.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Request Details</h2>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Type</span>
                    <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ ucfirst($changeRequest->type) }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Action</span>
                    <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ ucfirst($changeRequest->action) }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 mt-1 text-[10px] font-semibold rounded-full
                        {{ $changeRequest->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $changeRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $changeRequest->status === 'declined' ? 'bg-rose-100 text-rose-700' : '' }}">
                        {{ ucfirst($changeRequest->status) }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Requested By</span>
                    <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $changeRequest->user->name }}</span>
                </div>
            </div>

            @if($changeRequest->reason)
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Reason</span>
                <p class="text-xs text-gray-700 dark:text-gray-300">{{ $changeRequest->reason }}</p>
            </div>
            @endif

            <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Proposed Data</span>
                <div class="bg-gray-50 dark:bg-gray-900/30 rounded-xl p-4">
                    <pre class="text-[10px] text-gray-600 dark:text-gray-400 whitespace-pre-wrap font-mono">{{ json_encode($changeRequest->data, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>

            @if($changeRequest->reviewed_by && $changeRequest->reviewer)
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Reviewed By</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $changeRequest->reviewer->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Reviewed At</span>
                        <span class="block font-medium text-gray-800 dark:text-white mt-1">{{ $changeRequest->reviewed_at?->format('d M Y H:i') }}</span>
                    </div>
                </div>
                @if($changeRequest->review_notes)
                <div class="mt-3">
                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Review Notes</span>
                    <p class="text-xs text-gray-700 dark:text-gray-300">{{ $changeRequest->review_notes }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    @if($changeRequest->status === 'pending' && auth()->user()->hasRole('super-admin'))
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Review Request</h2>
        </div>
        <div class="p-5 space-y-4">
            <form action="{{ route('admin.change-requests.approve', $changeRequest) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Review Notes (optional)</label>
                    <textarea name="review_notes" rows="2" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Add notes about your decision..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Approve
                    </button>
                    <button type="submit" formaction="{{ route('admin.change-requests.decline', $changeRequest) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Decline
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
</x-app-layout>
