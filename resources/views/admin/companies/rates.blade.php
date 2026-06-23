<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }} - Rates</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Manage insurance rates for {{ $company->name }}</p>
            </div>
            <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Back to Companies</a>
        </div>

        @foreach($rates as $category => $categoryRates)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Item</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Value (KES)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Percentage (%)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Min Value</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Min Premium</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($categoryRates as $rate)
                                <tr>
                                    <form action="{{ route('admin.companies.rates.update', [$company, $rate]) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $rate->item_name }}</td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="value" value="{{ $rate->value }}" step="0.01"
                                                   class="w-32 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="percentage" value="{{ $rate->percentage }}" step="0.01" max="100"
                                                   class="w-24 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="min_value" value="{{ $rate->min_value }}" step="0.01"
                                                   class="w-32 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="min_premium" value="{{ $rate->min_premium }}" step="0.01"
                                                   class="w-32 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-2">Save</button>
                                            <a href="{{ route('admin.companies.rates.destroy', [$company, $rate]) }}" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Delete this rate?')">Delete</a>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Rate</h3>
            <form action="{{ route('admin.companies.rates.add', $company) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <input type="text" name="category" placeholder="e.g. Excess" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                    <input type="text" name="item_name" placeholder="e.g. Own Damage" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value Type</label>
                    <select name="value_type" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="fixed">Fixed Amount</option>
                        <option value="percentage">Percentage</option>
                        <option value="percentage_min">Percentage with Minimum</option>
                        <option value="text">Text</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value</label>
                    <input type="number" name="value" step="0.01"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Percentage (%)</label>
                    <input type="number" name="percentage" step="0.01" max="100"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min Value</label>
                    <input type="number" name="min_value" step="0.01"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Add Rate</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
