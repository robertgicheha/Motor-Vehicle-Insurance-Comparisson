<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">System</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Notification Details</h1>
                <p class="mt-0.5 text-xs text-slate-400">Full contents of the notification</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('notifications.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Notifications
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700 text-gray-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </span>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $notification->data['title'] ?? ($notification->data['message'] ?? 'Notification') }}</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        {{ $notification->created_at->format('M d, Y h:i A') }} &middot; {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-medium px-2 py-1 rounded-full shrink-0 {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-700 text-gray-500' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' }}">
                {{ $notification->read_at ? 'Read' : 'Unread' }}
            </span>
        </div>

        <div class="px-6 py-6">
            @if(!empty($notification->data['icon']) || !empty($notification->data['color']))
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-sm" style="background:{{ $notification->data['color'] ?? '#1756c8' }}">
                        <x-icon :name="$notification->data['icon'] ?? 'bell'" class="w-4 h-4" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold text-gray-800 dark:text-white">{{ $notification->data['title'] ?? '' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $notification->data['type'] ?? 'Notification' }}</p>
                    </div>
                </div>
            @endif

            <div class="prose prose-sm prose-gray dark:prose-invert max-w-none">
                @php
                    $body = $notification->data['body'] ?? $notification->data['message'] ?? 'No message body.';
                @endphp
                {{ $body }}
            </div>

            @php
                $meta = collect($notification->data)
                    ->filter(fn($v, $k) => in_array($k, ['message', 'title', 'body', 'icon', 'color', 'type', 'link']) === false && $v !== null);
            @endphp
            @if($meta->isNotEmpty())
                <div class="mt-6 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <p class="px-4 py-2 bg-gray-50 dark:bg-gray-700/40 text-[10px] font-semibold uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-700">Details</p>
                    <dl class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($meta as $key => $value)
                            <div class="px-4 py-2.5 flex items-start justify-between gap-4">
                                <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider shrink-0">{{ Str::title(str_replace('_', ' ', $key)) }}</dt>
                                <dd class="text-xs text-gray-800 dark:text-gray-200 text-right break-words">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if(!empty($notification->data['link']))
                <div class="mt-6">
                    <a href="{{ $notification->data['link'] }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                        View Details
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            @endif
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
            <p class="text-[10px] text-gray-400">Notification ID: {{ $notification->id }}</p>
            <div class="flex items-center gap-2">
                @if($notification->read_at)
                    <form action="{{ route('notifications.mark-unread', $notification->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-[10px] font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Mark as Unread
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifications.trash', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Move this notification to trash?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 text-[10px] font-medium border border-gray-200 dark:border-gray-600 text-rose-600 dark:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                        Trash
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
