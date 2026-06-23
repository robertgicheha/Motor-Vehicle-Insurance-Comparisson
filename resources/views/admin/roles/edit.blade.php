<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($role) ? 'Edit Role' : 'Create Role' }}</h1>
            <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Back</a>
        </div>

        <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            @csrf
            @if(isset($role)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions</label>
                @foreach($permissions as $group => $perms)
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">{{ $group }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($perms as $perm)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
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

            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                {{ isset($role) ? 'Update Role' : 'Create Role' }}
            </button>
        </form>
    </div>
</x-app-layout>
