<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles & Permissions</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Manage system roles and their permissions</p>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Add Role</a>
        </div>

        <div class="grid gap-6">
            @foreach($roles as $role)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $role->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $role->permissions->count() }} permissions</p>
                        </div>
                        @if(!in_array($role->name, ['super-admin']))
                            <div class="flex gap-2">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2">
                            @foreach($role->permissions as $perm)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $perm->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
