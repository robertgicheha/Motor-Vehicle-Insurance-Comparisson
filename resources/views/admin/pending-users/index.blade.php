<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="pendingUsersPage()">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Pending Users</h1>
                <p class="mt-0.5 text-xs text-slate-400">New self-registered accounts awaiting verification before they can log in.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    All Users
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-4 shadow-sm fade-in">
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl bg-red-500/10 border border-red-500/30 px-6 py-4 shadow-sm fade-in">
            <p class="text-sm font-medium text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ─── STAT STRIP ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Pending Verification</p>
            <p class="mt-1 text-2xl font-extrabold text-amber-500">{{ $totalPending }}</p>
        </div>
    </div>

    {{-- ─── SEARCH + BULK ACTIONS ───────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
            <form method="GET" class="flex-1 max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..."
                       class="w-full text-xs rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
            </form>
            <button type="button" x-show="selected.length > 0" x-cloak @click="confirmBulk()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Activate Selected (<span x-text="selected.length"></span>)
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-wider text-[10px] border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-3 text-left w-8">
                            <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Phone</th>
                        <th class="px-4 py-3 text-left">Registered</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($pendingUsers as $user)
                    <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $user->id }}" x-model="selected" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $user->mobile ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $user->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" @click="confirmOne({{ $user->id }}, @js($user->name), @js($user->email))"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg transition">
                                Activate
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No pending users</p>
                            <p class="text-xs text-gray-400 mt-1">Every self-registered account has been verified.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingUsers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $pendingUsers->links() }}
        </div>
        @endif
    </div>

    {{-- ─── CONFIRM MODAL ───────────────────────────────────────────────── --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="modalOpen = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="w-10 h-10 rounded-full bg-emerald-500/15 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-800 dark:text-white" x-text="modalMode === 'bulk' ? ('Activate ' + selected.length + ' user(s)?') : 'Activate this user?'"></h3>
            <p class="text-xs text-gray-400 mt-1">Confirm the account is genuine before activating. The user will be emailed and able to log in immediately.</p>

            <template x-if="modalMode === 'one'">
                <div class="mt-4 bg-gray-50 dark:bg-gray-900 rounded-xl p-4 text-xs space-y-1.5">
                    <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="font-semibold text-gray-800 dark:text-white" x-text="modalName"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="font-semibold text-gray-800 dark:text-white" x-text="modalEmail"></span></div>
                </div>
            </template>

            <div class="mt-5 flex items-center gap-2 justify-end">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancel</button>
                <button type="button" @click="submit()" class="px-4 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg transition">Confirm & Activate</button>
            </div>
        </div>
    </div>

    <form id="activateForm" method="POST" class="hidden">@csrf</form>
    <form id="bulkActivateForm" method="POST" action="{{ route('admin.pending-users.bulk-activate') }}" class="hidden">
        @csrf
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="user_ids[]" :value="id">
        </template>
    </form>
</div>

@push('scripts')
<script>
function pendingUsersPage() {
    return {
        selected: [],
        modalOpen: false,
        modalMode: 'one',
        modalUserId: null,
        modalName: '',
        modalEmail: '',
        toggleAll(e) {
            this.selected = e.target.checked ? @json($pendingUsers->pluck('id')) : [];
        },
        confirmOne(id, name, email) {
            this.modalMode = 'one';
            this.modalUserId = id;
            this.modalName = name;
            this.modalEmail = email;
            this.modalOpen = true;
        },
        confirmBulk() {
            this.modalMode = 'bulk';
            this.modalOpen = true;
        },
        submit() {
            if (this.modalMode === 'one') {
                var form = document.getElementById('activateForm');
                form.action = '{{ url("admin/pending-users") }}/' + this.modalUserId + '/activate';
                form.submit();
            } else {
                document.getElementById('bulkActivateForm').submit();
            }
        },
    };
}
</script>
@endpush

</x-app-layout>
