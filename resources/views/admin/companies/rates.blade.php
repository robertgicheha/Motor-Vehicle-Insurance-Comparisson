<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }} &mdash; Rates</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Manage insurance rates, cover limits, excesses and additional coverages</p>
            </div>
            <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&larr; Back to Companies</a>
        </div>

        @php
            $categoryIcons = [
                'Premium Rate' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'Cover Limits' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'Third Party Limits' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                'Excess' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z',
                'Additional Cover' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ];
        @endphp

        @foreach($rates as $category => $categoryRates)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $categoryIcons[$category] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                    </svg>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category }}</h3>
                    <span class="ml-auto text-xs text-gray-400">{{ $categoryRates->count() }} items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50">
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 w-56">Item</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400 w-24">Type</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Value (KES)</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">%</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Min Value</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Min Premium</th>
                                <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Notes</th>
                                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($categoryRates as $rate)
                                <tr>
                                    <form action="{{ route('admin.companies.rates.update', [$company, $rate]) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white text-sm">{{ $rate->item_name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <select name="value_type" class="px-1 py-0.5 text-xs rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                                <option value="fixed" {{ $rate->value_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="percentage" {{ $rate->value_type === 'percentage' ? 'selected' : '' }}>%</option>
                                                <option value="percentage_min" {{ $rate->value_type === 'percentage_min' ? 'selected' : '' }}>%+Min</option>
                                                <option value="text" {{ $rate->value_type === 'text' ? 'selected' : '' }}>Text</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="value" value="{{ $rate->value }}" step="0.01"
                                                   class="w-28 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="percentage" value="{{ $rate->percentage }}" step="0.01" max="100"
                                                   class="w-20 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="min_value" value="{{ $rate->min_value }}" step="0.01"
                                                   class="w-28 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="min_premium" value="{{ $rate->min_premium }}" step="0.01"
                                                   class="w-28 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-right text-sm">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="notes" value="{{ $rate->notes }}"
                                                   class="w-36 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                                   placeholder="Optional description">
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-2">Save</button>
                                            <a href="{{ route('admin.companies.rates.destroy', [$company, $rate]) }}"
                                               class="text-red-600 hover:text-red-800 text-sm"
                                               onclick="return confirm('Delete this rate?')">Delete</a>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Rate
            </h3>
            <form action="{{ route('admin.companies.rates.add', $company) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">Select category</option>
                        <option value="Premium Rate">Premium Rate</option>
                        <option value="Cover Limits">Cover Limits</option>
                        <option value="Third Party Limits">Third Party Limits</option>
                        <option value="Excess">Excess</option>
                        <option value="Additional Cover">Additional Cover</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Other Category</label>
                    <input type="text" name="category_other" placeholder="Or type custom"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                    <input type="text" name="item_name" placeholder="e.g. Windscreen / Glass" required
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
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Value (KES)</label>
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Min Premium</label>
                    <input type="number" name="min_premium" step="0.01"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Notes / Description</label>
                    <input type="text" name="notes" placeholder="e.g. Kes. 50,000/- min, Covered, etc."
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Add Rate</button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg text-green-700 dark:text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const catSelect = document.querySelector('select[name="category"]');
        const catOther = document.querySelector('input[name="category_other"]');
        catSelect.addEventListener('change', function() {
            if (this.value) catOther.value = '';
        });
        catOther.addEventListener('input', function() {
            if (this.value) catSelect.value = '';
        });
    });
    </script>
    @endpush
</x-app-layout>
