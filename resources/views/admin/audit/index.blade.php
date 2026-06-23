<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Trail</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">View system audit and activity logs</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.audit.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm {{ request()->routeIs('admin.audit.index') ? 'opacity-100' : 'opacity-70' }}">Audit Logs</a>
                <a href="{{ route('admin.audit.activity') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm {{ request()->routeIs('admin.audit.activity') ? 'opacity-100' : 'opacity-70' }}">Activity Logs</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="action" placeholder="Filter by action..." value="{{ request('action') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    @if(request()->routeIs('admin.audit.activity'))
                        <select name="level" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                            <option value="">All Levels</option>
                            <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                        </select>
                    @else
                        <select name="model" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                            <option value="">All Models</option>
                            <option value="User" {{ request('model') === 'User' ? 'selected' : '' }}>User</option>
                            <option value="Quotation" {{ request('model') === 'Quotation' ? 'selected' : '' }}>Quotation</option>
                            <option value="Company" {{ request('model') === 'Company' ? 'selected' : '' }}>Company</option>
                            <option value="Role" {{ request('model') === 'Role' ? 'selected' : '' }}>Role</option>
                        </select>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                            @if(!request()->routeIs('admin.audit.activity'))
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Model</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Model ID</th>
                            @endif
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Description</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 hidden md:table-cell">IP Address</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        @if($log->level === 'error') bg-red-100 text-red-800
                                        @elseif($log->level === 'warning') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                @if(!request()->routeIs('admin.audit.activity'))
                                    <td class="px-4 py-3">{{ $log->model ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-right">{{ $log->model_id ?? 'N/A' }}</td>
                                @endif
                                <td class="px-4 py-3 max-w-xs truncate">{{ $log->description ?? ($log->action) }}</td>
                                <td class="px-4 py-3 text-gray-500 hidden md:table-cell">{{ $log->ip_address ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-right text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">No logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
