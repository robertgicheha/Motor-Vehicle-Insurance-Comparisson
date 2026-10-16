<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Deleted Users</h1>
                <p class="mt-0.5 text-xs text-slate-400">Accounts removed by an admin. Restore any of these to reinstate the account.</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-white/80 hover:text-white border border-white/20 hover:bg-white/10 rounded-lg transition shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Users
            </a>
        </div>
    </div>

    {{-- ─── TABLE ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Username</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Role</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Deleted by</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Deleted at</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                        {{ strtoupper(substr($u->username, 0, 2)) }}
                                    </div>
                                    <span class="font-mono font-medium text-gray-800 dark:text-white">{{ $u->username }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                @foreach($u->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $u->deletedBy->username ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $u->deleted_at->diffForHumans() }}</td>
                            <td class="px-5 py-2.5 text-right">
                                <form action="{{ route('admin.users.restore', $u->id) }}" method="POST" onsubmit="return confirm('Restore this user?')" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-md transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No deleted users.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $users->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
