<aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-lg sidebar-transition"
       :class="sidebarOpen ? 'w-64' : 'w-20'"
       x-cloak>
    <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <div :class="sidebarOpen ? 'block' : 'hidden'" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span class="font-bold text-lg text-gray-800 dark:text-white">InsureDash</span>
        </div>
        <div :class="!sidebarOpen ? 'block mx-auto' : 'hidden'">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
            Dashboard
        </x-sidebar-link>

        @can('view quotations')
            <x-sidebar-link href="{{ route('quotations.index') }}" :active="request()->routeIs('quotations*')" icon="document">
                Quotations
            </x-sidebar-link>
            <x-sidebar-link href="{{ route('quotations.create') }}" :active="request()->routeIs('quotations.create')" icon="plus">
                New Quotation
            </x-sidebar-link>
        @endcan

        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500" :class="sidebarOpen ? 'block' : 'hidden'">Administration</p>
            </div>

            <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users*')" icon="users">
                User Management
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles*')" icon="shield">
                Roles & Permissions
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.companies.index') }}" :active="request()->routeIs('admin.companies*')" icon="building">
                Companies
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports*')" icon="chart">
                Reports
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.audit.index') }}" :active="request()->routeIs('admin.audit*')" icon="clock">
                Audit Trail
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.settings.index') }}" :active="request()->routeIs('admin.settings*')" icon="cog">
                Settings
            </x-sidebar-link>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div :class="sidebarOpen ? 'block' : 'hidden'">
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-medium">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
