<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-5 transition hover:shadow-md">
    <div class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $categoryIcons[$category] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
        </svg>
        <h3 class="text-xs font-semibold text-gray-900 dark:text-white">{{ $category }}</h3>
        <span class="ml-auto text-[10px] text-gray-400">{{ $categoryRates->count() }} items</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/40">
                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Item</th>
                    <th class="px-4 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Type</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Value (KES)</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">%</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Min Value</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Min Premium</th>
                    <th class="px-4 py-2.5 font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Notes</th>
                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($categoryRates as $rate)
                    <tr>
                        <form action="{{ route('admin.companies.rates.update', [$company, $rate]) }}" method="POST">
                            @csrf @method('PATCH')
                            <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-white text-xs">{{ $rate->item_name }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <select name="value_type" class="px-1.5 py-1 text-[10px] rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="fixed" {{ $rate->value_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="percentage" {{ $rate->value_type === 'percentage' ? 'selected' : '' }}>%</option>
                                    <option value="percentage_min" {{ $rate->value_type === 'percentage_min' ? 'selected' : '' }}>%+Min</option>
                                    <option value="text" {{ $rate->value_type === 'text' ? 'selected' : '' }}>Text</option>
                                </select>
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" name="value" value="{{ $rate->value }}" step="0.01"
                                       class="w-24 px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-right"
                                       placeholder="KES">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" name="percentage" value="{{ $rate->percentage }}" step="0.01" max="100"
                                       class="w-16 px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-right"
                                       placeholder="%">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" name="min_value" value="{{ $rate->min_value }}" step="0.01"
                                       class="w-24 px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-right"
                                       placeholder="KES">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="number" name="min_premium" value="{{ $rate->min_premium }}" step="0.01"
                                       class="w-24 px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none text-right"
                                       placeholder="KES">
                            </td>
                            <td class="px-4 py-2.5">
                                <input type="text" name="notes" value="{{ $rate->notes }}"
                                       class="w-28 px-2 py-1 text-xs rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none"
                                       placeholder="Optional">
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <button type="submit" class="text-[10px] font-medium text-blue-600 hover:text-blue-800 mr-2">Save</button>
                                <form action="{{ route('admin.companies.rates.destroy', [$company, $rate]) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete this rate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[10px] font-medium text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </form>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
