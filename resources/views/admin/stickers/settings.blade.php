<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Settings</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Sticker Email Settings</h1>
                <p class="mt-0.5 text-xs text-slate-400">Configure outbound/inbound email, templates, and track email delivery</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 p-0.5 rounded-lg">
                <a href="{{ route('admin.stickers.settings', ['tab' => 'email-config']) }}"
                   class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $tab === 'email-config' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                    Email Configuration
                </a>
                <a href="{{ route('admin.stickers.settings', ['tab' => 'email-templates']) }}"
                   class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $tab === 'email-templates' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                    Email Templates
                </a>
                <a href="{{ route('admin.stickers.settings', ['tab' => 'email-logs']) }}"
                   class="px-4 py-2 text-xs font-semibold rounded-lg transition {{ $tab === 'email-logs' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                    Email Logs
                </a>
            </div>
        </div>

        <div class="p-6">
            @if($tab === 'email-config')
                <form method="POST" action="{{ route('admin.stickers.settings.email') }}" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">From Email Address</label>
                            <input type="email" name="from_address" value="{{ $emailConfig['sticker_email_from_address'] ?? config('mail.from.address') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Outbound emails will be sent from this address (e.g. a noreply@ address)</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">From Name</label>
                            <input type="text" name="from_name" value="{{ $emailConfig['sticker_email_from_name'] ?? config('mail.from.name') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Reply-To Address</label>
                            <input type="email" name="reply_to" value="{{ $emailConfig['sticker_email_reply_to'] ?? config('mail.from.address') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Underwriters should reply to this address</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">CC Address (MIC)</label>
                            <input type="email" name="cc_address" value="{{ $emailConfig['sticker_email_cc'] ?? config('mail.from.address') }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Used when a company has no CC address of its own set</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Subject Template</label>
                            <input type="text" name="subject_template" value="{{ $emailConfig['sticker_email_subject_template'] ?? 'Sticker Issuance Request - {sticker_number}' }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Available variables: {!! '{'.'{sticker_number}'.'}' !!}, {!! '{'.'{customer_name}'.'}' !!}, {!! '{'.'{company}'.'}' !!}, {!! '{'.'{registration}'.'}' !!}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Turnaround Time (minutes)</label>
                            <input type="number" name="turnaround_minutes" value="{{ $emailConfig['sticker_turnaround_minutes'] ?? 30 }}" min="5" max="1440"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1">Expected response time from underwriter</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                            Save Configuration
                        </button>
                    </div>
                </form>

            @elseif($tab === 'email-templates')
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            <strong>Note:</strong> Email templates are managed through the <a href="{{ route('admin.email-settings.index') }}" class="underline">Email Templates</a> section. The sticker issuance template uses the key <code>certificate_issued</code>.
                        </p>
                    </div>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Available Variables for Sticker Issuance Template</h3>
                        </div>
                        <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{sticker_number}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Sticker ID</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{customer_name}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Customer full name</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{company}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Insurance company</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{registration}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Vehicle registration</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{premium}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Total premium amount</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{cover_type}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Cover type (Private/PSV)</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{underwriter_email}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Underwriter email</p></div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2"><code class="text-blue-600">{!! '{'.'{issued_at}'.'}' !!}</code><p class="text-gray-400 text-[10px] mt-0.5">Issuance date/time</p></div>
                        </div>
                    </div>
                </div>

            @elseif($tab === 'email-logs')
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-white">Email Delivery Log</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.stickers.settings', ['tab' => 'email-logs', 'filter' => 'failed']) }}" class="px-3 py-1 text-xs rounded-lg {{ request('filter') === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">Failed</a>
                            <a href="{{ route('admin.stickers.settings', ['tab' => 'email-logs', 'filter' => 'sent']) }}" class="px-3 py-1 text-xs rounded-lg {{ request('filter') === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">Sent</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/40">
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sticker</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Type</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Recipient/Sender</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Subject</th>
                                    <th class="px-4 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sent At</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($recentLogs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $log->sticker->sticker_number }}</td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $log->isOutbound() ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                                {{ $log->isOutbound() ? 'Outbound' : 'Inbound' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->recipient ?? $log->sender }}</td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $log->subject }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            @php
                                                $color = match($log->status) {
                                                    'sent', 'delivered', 'opened' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                    'failed', 'bounced' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                    'received', 'processing', 'completed' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                                    default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $color }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400">{{ $log->sent_at?->format('d M Y, H:i') ?? '-' }}</td>
                                        <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $log->error_message ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No email logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($recentLogs->hasPages())
                        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $recentLogs->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
