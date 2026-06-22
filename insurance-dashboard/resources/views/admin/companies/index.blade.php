<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Insurance Companies</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Manage companies and their rates</p>
            </div>
            <a href="{{ route('admin.companies.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Add Company</a>
        </div>

        <div class="grid gap-6">
            @foreach($companies as $company)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($company->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $company->quotations_count }} quotations</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $company->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <a href="{{ route('admin.companies.rates', $company) }}" class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">Rates</a>
                            <a href="{{ route('admin.companies.edit', $company) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Edit</a>
                            <a href="{{ route('admin.companies.toggle-status', $company) }}" class="px-3 py-1.5 text-sm {{ $company->is_active ? 'text-red-600' : 'text-green-600' }} hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
