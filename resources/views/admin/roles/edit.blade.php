<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">{{ isset($role) ? 'Edit Role' : 'Create Role' }}</h1>
                <p class="mt-0.5 text-xs text-slate-400">{{ isset($role) ? 'Update role name and permissions.' : 'Define a new role and assign permissions.' }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.roles.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    {{-- ─── FORM CARD ─────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Role Details</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">All fields marked with * are required.</p>
        </div>

        <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="p-5 space-y-5">
            @csrf
            @if(isset($role)) @method('PUT') @endif

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Role Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Permissions</label>
                @foreach($permissions as $group => $perms)
                    <div class="mb-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">{{ $group }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($perms as $perm)
                                <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                           {{ isset($role) && $role->hasPermissionTo($perm->name) ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    {{ $perm->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    {{ isset($role) ? 'Update Role' : 'Create Role' }}
                </button>
                <a href="{{ route('admin.roles.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
