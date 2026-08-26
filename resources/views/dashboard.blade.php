<x-app-layout>
    <div class="space-y-6 pt-10 fade-in">
        @php
            $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
            $pendingStickers = $isAdmin ? \App\Models\Sticker::where('status', 'issued')->count() : 0;
            $completedToday = $isAdmin ? \App\Models\Sticker::where('status', 'completed')->whereDate('updated_at', today())->count() : 0;
            $totalStickers = $isAdmin ? \App\Models\Sticker::count() : 0;
        @endphp

        @if($isAdmin && $pendingStickers > 0)
            <div class="rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-5 shadow-lg">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ $pendingStickers }} certificate{{ $pendingStickers > 1 ? 's' : '' }} awaiting completion</p>
                            <p class="text-xs text-white/80">Go to Update Stickers to mark certificates as completed</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.stickers.update-form') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-all duration-200 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Update Stickers
                    </a>
                </div>
            </div>
        @endif

        <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Welcome back</p>
                    <h1 class="mt-0.5 text-xl font-bold text-white">Dashboard</h1>
                    <p class="mt-0.5 text-xs text-slate-400">Overview of your insurance platform</p>
                </div>
            </div>
        </div>

        @if($isAdmin)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Stickers</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ number_format($totalStickers) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Awaiting Completion</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $pendingStickers }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Completed Today</p>
                            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $completedToday }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">
                {{ __("You're logged in!") }}
            </div>
        </div>
    </div>
</x-app-layout>
