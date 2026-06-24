<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">User Management</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage system accounts, roles and access.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.users.export') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </a>
                <button onclick="document.getElementById('importFile').click()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4l4-4m0 0l4-4m-4 4V4"/></svg>
                    Import
                </button>
                <form id="importForm" action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="file" id="importFile" accept=".xlsx,.csv" onchange="document.getElementById('importForm').submit()">
                </form>
                <button onclick="openAddModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add User
                </button>
            </div>
        </div>

        {{-- Stat strip --}}
        {{-- KPI row --}}
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="kpi-card p-4">
                    <div class="accent-bar"></div>
                    <p class="kpi-eyebrow">Total Users</p>
                    <p class="kpi-value mt-1">{{ $totalUsers }}</p>
                    <span class="kpi-delta neutral mt-1.5 inline-block">
                        Registered accounts
                    </span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar"
                        style="background:linear-gradient(90deg,#34d399,#6ee7b7)">
                    </div>
                    <p class="kpi-eyebrow">Online Now</p>
                    <p class="kpi-value mt-1">{{ $onlineUsers }}</p>

                    @if($onlineUsers > 0)
                        <span class="kpi-delta up mt-1.5 inline-block">
                            ↑ Active users
                        </span>
                    @else
                        <span class="kpi-delta neutral mt-1.5 inline-block">
                            No active sessions
                        </span>
                    @endif
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar"
                        style="background:linear-gradient(90deg,#818cf8,#a5b4fc)">
                    </div>
                    <p class="kpi-eyebrow">Male Users</p>
                    <p class="kpi-value mt-1">{{ $maleCount }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">
                        {{ $totalUsers > 0 ? round(($maleCount / $totalUsers) * 100) : 0 }}% of users
                    </span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar"
                        style="background:linear-gradient(90deg,#fb7185,#fda4af)">
                    </div>
                    <p class="kpi-eyebrow">Female Users</p>
                    <p class="kpi-value mt-1">{{ $femaleCount }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">
                        {{ $totalUsers > 0 ? round(($femaleCount / $totalUsers) * 100) : 0 }}% of users
                    </span>
                </div>
            </div>
    </div>

    {{-- ─── CARDS GRID ─────────────────────────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Directory</p>
            <span class="text-xs text-gray-400">Showing {{ min($users->count(), 15) }} of {{ $totalUsers }}</span>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-5 lg:grid-cols-6 xl:grid-cols-6 gap-3" id="cardsGrid">
            @foreach($users->take(15) as $u)
            <div class="aspect-square bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col items-center justify-center gap-1.5 p-3 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition group cursor-pointer"
                 title="{{ $u->name }}">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold select-none">
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                    </div>
                    @if($u->isOnline())
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                    @endif
                </div>
                {{-- Name --}}
                <p class="text-[11px] font-semibold text-gray-800 dark:text-white text-center leading-tight line-clamp-1 w-full">{{ $u->name }}</p>
                {{-- Role badge --}}
                @php $firstRole = $u->roles->first(); @endphp
                @if($firstRole)
                    <span class="text-[9px] font-medium px-1.5 py-0.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 leading-none">{{ $firstRole->name }}</span>
                @endif
                {{-- Status dot --}}
                <span class="inline-flex items-center gap-1 text-[9px] {{ $u->is_active ? 'text-emerald-600' : 'text-rose-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-rose-400' }}"></span>
                    {{ $u->is_active ? 'Active' : 'Inactive' }}
                </span>
                {{-- Quick actions on hover --}}
                <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition">
                    <a href="{{ route('admin.users.edit', $u) }}" class="text-[9px] font-medium text-blue-600 hover:underline">Edit</a>
                    <span class="text-gray-300 dark:text-gray-600">·</span>
                    <a href="{{ route('admin.users.toggle-status', $u) }}" class="text-[9px] font-medium {{ $u->is_active ? 'text-rose-500' : 'text-emerald-600' }} hover:underline">
                        {{ $u->is_active ? 'Disable' : 'Enable' }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        @if($users->count() > 15)
        <div class="mt-4 text-center">
            <button onclick="scrollToTable()"
                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                See all {{ $totalUsers }} users
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        @endif
    </div>

    {{-- ─── TABLE ───────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden" id="usersTable">
        {{-- Table header / filters --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">All Users</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Full directory with search and filters</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" placeholder="Search…" value="{{ request('search') }}"
                       class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none w-44">
                <select name="gender" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="">All genders</option>
                    <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
                <select name="status" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                    <option value="">All status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="px-3 py-1.5 text-xs border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Reset</a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/40">
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">User</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Email</th>
                        <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Mobile</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Gender</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Role</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Online</th>
                        <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $u->email }}</td>
                            <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $u->mobile ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-center capitalize text-gray-500 dark:text-gray-400">{{ $u->gender ?? '—' }}</td>
                            <td class="px-5 py-2.5 text-center">
                                @foreach($u->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-center">
                                @if($u->isOnline())
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>Online
                                    </span>
                                @else
                                    <span class="text-[10px] text-gray-400">Offline</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.users.toggle-status', $u) }}"
                                       class="p-1.5 {{ $u->is_active ? 'text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }} rounded-md transition"
                                       title="{{ $u->is_active ? 'Deactivate' : 'Activate' }}">
                                        @if($u->is_active)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </a>
                                    @if(!$u->hasRole('super-admin'))
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Delete this user?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-md transition" title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $users->links() }}</div>
        @endif
    </div>
</div>

{{-- ─── ADD USER MODAL ──────────────────────────────────────────────────────── --}}
<div id="addUserModal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAddModal()"></div>

    {{-- Panel --}}
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-900 shadow-2xl flex flex-col">
        {{-- Modal header --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between shrink-0">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Add New User</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">Fill in the details to create a new account.</p>
            </div>
            <button onclick="closeAddModal()" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form --}}
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Jane Wanjiku"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="jane@example.com"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" required placeholder="Jane"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" required placeholder="Wanjiku"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Mobile</label>
                        <input type="text" name="mobile" placeholder="+254 700 000 000"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Gender</label>
                        <select name="gender"
                                class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Role <span class="text-rose-500">*</span></label>
                    <select name="role" required
                            class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Select a role</option>
                       @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-gray-700 dark:text-gray-300">Active account</span>
                    </label>
                </div>

                {{-- Validation errors (AJAX) --}}
                <div id="modalErrors" class="hidden rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 px-3 py-2 text-xs text-rose-600 dark:text-rose-400 space-y-0.5"></div>
            </form>
        </div>

        {{-- Modal footer --}}
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2 shrink-0">
            <button onclick="closeAddModal()" class="px-4 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">Cancel</button>
            <button onclick="submitAddUser()" id="addUserBtn"
                    class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center gap-2">
                <span id="addUserBtnText">Create User</span>
                <svg id="addUserSpinner" class="w-3.5 h-3.5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addUserModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('addUserForm').reset();
    document.getElementById('modalErrors').classList.add('hidden');
    document.getElementById('modalErrors').innerHTML = '';
}

function scrollToTable() {
    document.getElementById('usersTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function submitAddUser() {
    const form   = document.getElementById('addUserForm');
    const btn    = document.getElementById('addUserBtn');
    const text   = document.getElementById('addUserBtnText');
    const spin   = document.getElementById('addUserSpinner');
    const errors = document.getElementById('modalErrors');

    errors.classList.add('hidden');
    errors.innerHTML = '';
    btn.disabled = true;
    text.textContent = 'Creating…';
    spin.classList.remove('hidden');

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: new FormData(form),
    })
    .then(async res => {
        if (res.redirected || res.ok) {
            // Success — reload to show new user
            window.location.reload();
            return;
        }
        const data = await res.json();
        if (data.errors) {
            errors.classList.remove('hidden');
            errors.innerHTML = Object.values(data.errors).flat()
                .map(e => `<p>· ${e}</p>`).join('');
        }
    })
    .catch(() => {
        errors.classList.remove('hidden');
        errors.innerHTML = '<p>Something went wrong. Please try again.</p>';
    })
    .finally(() => {
        btn.disabled = false;
        text.textContent = 'Create User';
        spin.classList.add('hidden');
    });
}

// ESC to close
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAddModal(); });
</script>
</x-app-layout>