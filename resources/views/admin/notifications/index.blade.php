<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Notifications</h1>
                <p class="mt-0.5 text-xs text-slate-400">View and manage system notifications</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($notifications->count() > 0)
                    <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark All Read
                        </button>
                    </form>
                    <form action="{{ route('admin.notifications.clear-all') }}" method="POST" class="inline" onsubmit="return confirm('Clear all notifications?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Clear All
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total</p>
                <p class="kpi-value mt-1">{{ $notifications->total() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All notifications</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Unread</p>
                <p class="kpi-value mt-1">{{ auth()->user()->unreadNotifications()->count() }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Pending attention</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Read</p>
                <p class="kpi-value mt-1">{{ $notifications->total() - auth()->user()->unreadNotifications()->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Already viewed</span>
            </div>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Rate</p>
                <p class="kpi-value mt-1">{{ $notifications->total() > 0 ? round((($notifications->total() - auth()->user()->unreadNotifications()->count()) / $notifications->total()) * 100) : 0 }}%</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Read rate</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Notification History</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $notifications->total() }} total notifications</p>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <div class="px-5 py-3 flex items-start gap-3 {{ $notification->read_at ? '' : 'bg-blue-50/50 dark:bg-blue-900/10' }}">
                    <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $notification->read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-blue-500' }}"></span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.notifications.show', $notification->id) }}"
                           class="inline-flex items-start gap-1.5 group">
                            <p class="text-xs text-gray-800 dark:text-white font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $notification->data['message'] ?? 'Notification' }}</p>
                            <svg class="w-3 h-3 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        @if($notification->read_at)
                            <form action="{{ route('admin.notifications.mark-unread', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded transition" title="Mark as unread">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-blue-600 hover:text-blue-800 rounded transition" title="Mark as read">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notification?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 rounded transition" title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p class="text-xs text-gray-400">No notifications yet.</p>
                </div>
            @endforelse
        </div>
        @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
