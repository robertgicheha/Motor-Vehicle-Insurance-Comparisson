<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 fade-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quotation Details</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $quotation->quotation_number }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('quotations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">New Quotation</a>
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Back</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold">{{ $quotation->company->name }}</h2>
                        <p class="opacity-90">{{ $quotation->car_make }} {{ $quotation->car_model }} ({{ $quotation->car_year }})</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-75">Total Premium</p>
                        <p class="text-3xl font-bold">KES {{ number_format($quotation->total_premium, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quotation #</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $quotation->quotation_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Car Registration</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $quotation->car_registration ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Car Valuation</p>
                    <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($quotation->car_valuation, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Basic Premium</p>
                    <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($quotation->premium, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">PVT Premium</p>
                    <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($quotation->pvt_premium, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ ucfirst($quotation->status) }}</span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Excess - Own Damage</p>
                    <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($quotation->excess_own_damage ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Excess - Theft</p>
                    <p class="font-semibold text-gray-900 dark:text-white">KES {{ number_format($quotation->excess_theft ?? 0, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Generated On</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $quotation->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>

        @if($quotation->breakdown)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Premium Breakdown</h3>
                <div class="space-y-2">
                    @foreach($quotation->breakdown as $key => $value)
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                            <span class="font-semibold">{{ is_numeric($value) ? 'KES ' . number_format($value, 2) : $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
