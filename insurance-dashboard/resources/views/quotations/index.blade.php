<x-app-layout>
    <div class="space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotations</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Manage all generated quotations</p>
            </div>
            <a href="{{ route('quotations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">New Quotation</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                            @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
                            @endif
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Company</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Vehicle</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Valuation</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Premium</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($quotations as $q)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('quotations.show', $q) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $q->quotation_number }}</a>
                                </td>
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
                                    <td class="px-4 py-3">{{ $q->user->name }}</td>
                                @endif
                                <td class="px-4 py-3">{{ $q->company->name }}</td>
                                <td class="px-4 py-3">{{ $q->car_make }} {{ $q->car_model }} ({{ $q->car_year }})</td>
                                <td class="px-4 py-3 text-right">{{ number_format($q->car_valuation, 0) }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($q->total_premium, 0) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ ucfirst($q->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-500">{{ $q->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('quotations.show', $q) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <form action="{{ route('quotations.destroy', $q) }}" method="POST" onsubmit="return confirm('Delete this quotation?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No quotations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($quotations->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $quotations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
