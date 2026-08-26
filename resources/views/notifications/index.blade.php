<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Notifications</h1>
                <p class="mt-0.5 text-xs text-slate-400">View and manage your notifications</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($tab !== 'trash' && $notifications->total() > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark All Read
                        </button>
                    </form>
                @endif
                @if($tab === 'trash' && $trashedCount > 0)
                    <form action="{{ route('notifications.empty-trash') }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete all trashed notifications?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Empty Trash
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('notifications.index', ['tab' => 'all']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total</p>
                <p class="kpi-value mt-1">{{ $totalCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All notifications</span>
            </a>
            <a href="{{ route('notifications.index', ['tab' => 'unread']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Unread</p>
                <p class="kpi-value mt-1">{{ $unreadCount }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Pending attention</span>
            </a>
            <a href="{{ route('notifications.index', ['tab' => 'read']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Read</p>
                <p class="kpi-value mt-1">{{ $readCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Already viewed</span>
            </a>
            <a href="{{ route('notifications.index', ['tab' => 'trash']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Trash</p>
                <p class="kpi-value mt-1">{{ $trashedCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Deleted items</span>
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">
                    @switch($tab)
                        @case('unread') Unread Notifications @break
                        @case('read') Read Notifications @break
                        @case('trash') Trashed Notifications @break
                        @default All Notifications
                    @endswitch
                </h2>
                <p class="text-[10px] text-gray-400 mt-0.5">{{ $notifications->total() }} {{ Str::plural('notification', $notifications->total()) }}</p>
            </div>
            <div class="flex items-center gap-1.5">
                <a href="{{ route('notifications.index', ['tab' => 'all']) }}"
                   class="px-3 py-1.5 text-xs rounded-lg font-medium transition {{ $tab === 'all' ? 'bg-blue-600 text-white' : 'border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">All</a>
                <a href="{{ route('notifications.index', ['tab' => 'unread']) }}"
                   class="px-3 py-1.5 text-xs rounded-lg font-medium transition {{ $tab === 'unread' ? 'bg-emerald-600 text-white' : 'border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">Unread</a>
                <a href="{{ route('notifications.index', ['tab' => 'read']) }}"
                   class="px-3 py-1.5 text-xs rounded-lg font-medium transition {{ $tab === 'read' ? 'bg-indigo-600 text-white' : 'border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">Read</a>
                <a href="{{ route('notifications.index', ['tab' => 'trash']) }}"
                   class="px-3 py-1.5 text-xs rounded-lg font-medium transition {{ $tab === 'trash' ? 'bg-rose-600 text-white' : 'border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">Trash</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px] w-10">
                            <input type="checkbox" id="selectAll"
                                   class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Message</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Received</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($notifications as $notification)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ $tab !== 'trash' && !$notification->read_at ? 'bg-blue-50/30 dark:bg-blue-900/5' : '' }}">
                            <td class="px-5 py-2.5">
                                <input type="checkbox" value="{{ $notification->id }}"
                                       class="notification-checkbox w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-5 py-2.5">
                                <span class="w-2 h-2 rounded-full inline-block mt-0.5
                                    {{ $tab === 'trash' ? 'bg-red-400' : ($notification->read_at ? 'bg-gray-300 dark:bg-gray-600' : 'bg-blue-500') }}">
                                </span>
                            </td>
                            <td class="px-5 py-2.5">
                                <a href="{{ route('notifications.show', $notification->id) }}"
                                   class="inline-flex items-start gap-1.5 group max-w-md">
                                    <p class="text-xs text-gray-800 dark:text-white font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        {{ $notification->data['message'] ?? 'Notification' }}
                                    </p>
                                    <svg class="w-3 h-3 text-gray-300 dark:text-gray-600 group-hover:text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            </td>
                            <td class="px-5 py-2.5 text-right text-[10px] text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($tab === 'trash')
                                        <form action="{{ route('notifications.restore', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-md transition" title="Restore">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this notification?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition" title="Delete permanently">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        @if($notification->read_at)
                                            <form action="{{ route('notifications.mark-unread', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md transition" title="Mark as unread">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="Mark as read">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('notifications.trash', $notification->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-500 dark:hover:text-rose-400 rounded-md transition" title="Trash">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <p class="text-xs text-gray-400">
                                    @switch($tab)
                                        @case('unread') No unread notifications. @break
                                        @case('read') No read notifications. @break
                                        @case('trash') Trash is empty. @break
                                        @default No notifications yet.
                                    @endswitch
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tab !== 'trash' && $notifications->total() > 0)
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2" id="bulkActions">
                <span class="text-[10px] text-gray-400" id="selectedCount">0 selected</span>
                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline" id="bulkMarkReadForm">
                    @csrf
                    <input type="hidden" name="ids" id="bulkMarkReadIds">
                    <button type="submit" class="px-3 py-1.5 text-[10px] font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Mark Read
                    </button>
                </form>
                <form action="{{ route('notifications.trash-all') }}" method="POST" class="inline" id="bulkTrashForm">
                    @csrf
                    <input type="hidden" name="ids" id="bulkTrashIds">
                    <button type="submit" class="px-3 py-1.5 text-[10px] font-medium border border-gray-200 dark:border-gray-600 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Trash
                    </button>
                </form>
            </div>
        @endif

        @if($tab === 'trash' && $trashedCount > 0)
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <span class="text-[10px] text-gray-400">{{ $trashedCount }} {{ Str::plural('notification', $trashedCount) }} in trash</span>
                <form action="{{ route('notifications.empty-trash') }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete all trashed notifications?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-[10px] font-medium border border-gray-200 dark:border-gray-600 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Empty Trash
                    </button>
                </form>
            </div>
        @endif

        @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const bulkMarkReadIds = document.getElementById('bulkMarkReadIds');
    const bulkTrashIds = document.getElementById('bulkTrashIds');

    function updateSelected() {
        const checked = document.querySelectorAll('.notification-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        selectedCount.textContent = ids.length + ' selected';
        bulkMarkReadIds.value = JSON.stringify(ids);
        bulkTrashIds.value = JSON.stringify(ids);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelected();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelected);
    });

    updateSelected();
});
</script>
</x-app-layout>
