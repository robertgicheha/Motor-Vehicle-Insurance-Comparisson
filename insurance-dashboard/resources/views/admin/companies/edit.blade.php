<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($company) ? 'Edit Company' : 'Add Company' }}</h1>
            <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Back</a>
        </div>

        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            @csrf
            @if(isset($company)) @method('PUT') @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500">{{ old('description', $company->description ?? '') }}</textarea>
            </div>
            @if(isset($company))
            <div>
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_active" value="1" {{ $company->is_active ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                    Active
                </label>
            </div>
            @endif
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                {{ isset($company) ? 'Update' : 'Create' }} Company
            </button>
        </form>
    </div>
</x-app-layout>
