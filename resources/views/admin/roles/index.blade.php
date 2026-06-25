<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Roles & Permissions</h1>
                <p class="mt-0.5 text-xs text-slate-400">Manage system roles and their permissions</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.roles.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add Role
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('admin.roles.index') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar"></div>
                <p class="kpi-eyebrow">Total Roles</p>
                <p class="kpi-value mt-1">{{ $roles->count() }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">System roles</span>
            </a>
            <a href="{{ route('admin.roles.create') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                <p class="kpi-eyebrow">Permissions</p>
                <p class="kpi-value mt-1">{{ $roles->sum(fn($r) => $r->permissions->count()) }}</p>
                <span class="kpi-delta up mt-1.5 inline-block">Total permissions</span>
            </a>
            <div class="kpi-card p-4">
                <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                <p class="kpi-eyebrow">Super Admin</p>
                <p class="kpi-value mt-1">{{ $roles->where('name', 'super-admin')->count() ? 'Yes' : 'No' }}</p>
                <span class="kpi-delta neutral mt-1.5 inline-block">Built-in role</span>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="kpi-card p-4 block hover:opacity-80 transition">
                <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                <p class="kpi-eyebrow">Custom Roles</p>
                <p class="kpi-value mt-1">{{ $roles->where('name', '!=', 'super-admin')->count() }}</p>
                <span class="kpi-delta down mt-1.5 inline-block">Editable roles</span>
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($roles as $role)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-micblue flex items-center justify-center text-blue-400 font-bold text-xs uppercase">
                            {{ substr($role->name, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-gray-800 dark:text-white capitalize">{{ $role->name }}</h3>
                            <p class="text-[10px] text-gray-400">{{ $role->permissions->count() }} permissions</p>
                        </div>
                    </div>
                    @if(!in_array($role->name, ['super-admin']))
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.roles.edit', $role) }}"
                               class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">Edit</a>
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition">Delete</button>
                            </form>
                        </div>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Protected</span>
                    @endif
                </div>
                <div class="px-5 py-4">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($role->permissions as $perm)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $perm->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</x-app-layout>
