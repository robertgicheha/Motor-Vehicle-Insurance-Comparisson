<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Support</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Help & Support</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage user enquiries and support requests</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.help.index', ['status' => 'open']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Open</p>
                <p class="kpi-value mt-1">{{ $openCount }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Needs attention</span>
            </a>
            <a href="{{ route('admin.help.index', ['status' => 'in_progress']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fbbf24,#fcd34d)"></div>
                <p class="kpi-eyebrow">In Progress</p>
                <p class="kpi-value mt-1">{{ $inProgressCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Being handled</span>
            </a>
            <a href="{{ route('admin.help.index', ['status' => 'resolved']) }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Resolved</p>
                <p class="kpi-value mt-1">{{ $resolvedCount }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Completed</span>
            </a>
            <div class="kpi-card p-4">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total</p>
                <p class="kpi-value mt-1">{{ $openCount + $inProgressCount + $resolvedCount }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">All enquiries</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Enquiries</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">User support requests</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Name</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Subject</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enquiries as $enquiry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold">{{ strtoupper(substr($enquiry->name, 0, 2)) }}</div>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $enquiry->name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $enquiry->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $enquiry->subject }}</td>
                            <td class="px-5 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    {{ $enquiry->status === 'open' ? 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : '' }}
                                    {{ $enquiry->status === 'in_progress' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $enquiry->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right text-gray-500 dark:text-gray-400">{{ $enquiry->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.help.show', $enquiry) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="View">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.help.update-status', $enquiry) }}" method="POST" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-[10px] border border-gray-200 dark:border-gray-600 rounded px-1.5 py-1 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            <option value="open" {{ $enquiry->status === 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="in_progress" {{ $enquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $enquiry->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No enquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($enquiries, 'hasPages') && $enquiries->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $enquiries->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
