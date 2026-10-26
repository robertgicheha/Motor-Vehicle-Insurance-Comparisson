<x-app-layout>
<div class="max-w-5xl mx-auto space-y-6 pt-10 fade-in" x-data="{ open: false, selected: null }">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System Logs</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">User Story</h1>
                <p class="mt-0.5 text-xs text-slate-400">Search a user and see everything they did, start to finish</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.audit.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition border border-white/20 text-white/80 hover:bg-white/10">
                    Audit Logs
                </a>
                <a href="{{ route('admin.audit.activity') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition border border-white/20 text-white/80 hover:bg-white/10">
                    Activity Logs
                </a>
                <a href="{{ route('admin.audit.story') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition bg-blue-500 text-white">
                    User Story
                </a>
            </div>
        </div>

        <form method="GET" class="mt-6 flex flex-col sm:flex-row items-end gap-3 max-w-xl">
            <div class="flex-1 w-full">
                <label class="block text-[10px] font-semibold text-blue-200 uppercase tracking-wider mb-1">Select User</label>
                <select name="user_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 text-sm border-0 rounded-lg bg-white/95 text-gray-800 focus:ring-2 focus:ring-blue-300 outline-none">
                    <option value="">Search for a user...</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $selectedUser?->id === $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if(!$selectedUser)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-10 text-center">
            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <p class="text-xs text-gray-400">Select a user above to see their full activity story.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">{{ $selectedUser->name }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $selectedUser->username }}@if($selectedUser->branch) &middot; {{ $selectedUser->branch }}@endif &middot; {{ $timeline->total() }} events</p>
                </div>
            </div>

            <div class="p-5">
                @forelse($timeline as $entry)
                    <div class="relative pl-8 pb-6 last:pb-0 border-l-2 border-gray-100 dark:border-gray-700 last:border-transparent cursor-pointer group"
                         @click="selected = {{ Illuminate\Support\Js::from($entry) }}; open = true">
                        <div class="absolute -left-[7px] top-0 w-3 h-3 rounded-full
                            {{ $entry->level === 'error' ? 'bg-red-500' : ($entry->level === 'warning' ? 'bg-amber-500' : ($entry->type === 'audit' ? 'bg-purple-500' : 'bg-blue-500')) }}"></div>
                        <div class="rounded-xl border border-gray-100 dark:border-gray-700 group-hover:border-blue-300 dark:group-hover:border-blue-600 group-hover:bg-blue-50/50 dark:group-hover:bg-blue-900/10 transition p-3.5">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                                    {{ $entry->type === 'audit' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                    {{ $entry->action }}
                                </span>
                                <span class="text-[10px] text-gray-400 shrink-0">{{ $entry->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            @if($entry->description && $entry->description !== $entry->action)
                                <p class="text-xs text-gray-700 dark:text-gray-300 mt-1.5">{{ $entry->description }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-6">No activity recorded for this user yet.</p>
                @endforelse
            </div>

            @if($timeline->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $timeline->links() }}</div>
            @endif
        </div>
    @endif

    {{-- ─── ENTRY DETAIL MODAL ─────────────────────────────────────────── --}}
    <div x-show="open" x-cloak @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="open = false">
        <div x-show="open" x-transition class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg max-h-[85vh] overflow-y-auto">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-800 dark:text-white">Event Details</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 space-y-3 text-xs" x-show="selected">
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">User</span>
                    <span class="font-semibold text-gray-800 dark:text-white text-right">{{ $selectedUser->name ?? '' }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Date</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.created_at ? new Date(selected.created_at).toLocaleDateString('en-GB') : ''"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Time</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.created_at ? new Date(selected.created_at).toLocaleTimeString() : ''"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">Activity</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.action"></span>
                </div>
                <div class="flex justify-between gap-3" x-show="selected?.description">
                    <span class="text-gray-400 shrink-0">Description</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.description"></span>
                </div>
                <div class="flex justify-between gap-3" x-show="selected?.model">
                    <span class="text-gray-400 shrink-0">Model</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="(selected?.model || '') + (selected?.model_id ? ' #' + selected.model_id : '')"></span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-gray-400 shrink-0">IP Address</span>
                    <span class="font-medium text-gray-800 dark:text-white text-right" x-text="selected?.ip_address ?? 'N/A'"></span>
                </div>
                <div x-show="selected?.user_agent">
                    <span class="text-gray-400 block mb-1">User Agent</span>
                    <span class="font-medium text-gray-800 dark:text-white break-all text-[11px]" x-text="selected?.user_agent"></span>
                </div>
                <div x-show="selected?.old_values" class="border-t border-gray-100 dark:border-gray-700 pt-3">
                    <span class="text-gray-400 block mb-1">Before</span>
                    <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5 text-[10px] overflow-x-auto" x-text="JSON.stringify(selected?.old_values, null, 2)"></pre>
                </div>
                <div x-show="selected?.new_values" class="pt-1">
                    <span class="text-gray-400 block mb-1">After</span>
                    <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2.5 text-[10px] overflow-x-auto" x-text="JSON.stringify(selected?.new_values, null, 2)"></pre>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
