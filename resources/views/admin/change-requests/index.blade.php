<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Approvals</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">
                    {{ $isSuperAdmin ? 'Pending Approvals' : 'My Change Requests' }}
                </h1>
                <p class="mt-0.5 text-xs text-slate-400">
                    {{ $isSuperAdmin ? 'Review and approve or decline change requests from admins.' : 'Track your pending and resolved change requests.' }}
                </p>
            </div>
            @if($pendingCount > 0)
                <div class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-lg">
                    <span class="w-2 h-2 bg-amber-400 rounded-full"></span>
                    {{ $pendingCount }} pending
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        @if($isSuperAdmin)<th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Requested By</th>@endif
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($requests as $r)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        @if($isSuperAdmin)
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-[10px] font-bold text-blue-600">
                                    {{ strtoupper(substr($r->user->first_name ?? $r->user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $r->user->name }}</span>
                            </div>
                        </td>
                        @endif
                        <td class="px-5 py-2.5">
                            <span class="font-medium text-gray-800 dark:text-white">{{ ucfirst($r->type) }}</span>
                        </td>
                        <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ ucfirst($r->action) }}</td>
                        <td class="px-5 py-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full
                                {{ $r->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                {{ $r->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}
                                {{ $r->status === 'declined' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : '' }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $r->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-2.5 text-right">
                            <a href="{{ route('admin.change-requests.show', $r) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-[10px] font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isSuperAdmin ? 6 : 5 }}" class="px-5 py-10 text-center text-xs text-gray-400">
                            No change requests yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
</x-app-layout>
