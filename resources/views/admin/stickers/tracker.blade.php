<x-app-layout>
<div class="space-y-6 pt-10 fade-in" x-data="stickerTrackerLookup()">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Stickers</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Sticker Tracker</h1>
                <p class="mt-0.5 text-xs text-slate-400">Search and track all issued stickers</p>
            </div>
            <div class="flex items-center gap-1 bg-white/10 p-0.5 rounded-lg shrink-0">
                <a href="{{ route('admin.stickers.tracker', array_merge(request()->except('view', 'page'), ['view' => 'all'])) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $view === 'all' ? 'bg-white text-gray-800' : 'text-white/80 hover:text-white' }}">
                    All Stickers ({{ $allCount }})
                </a>
                <a href="{{ route('admin.stickers.tracker', array_merge(request()->except('view', 'page'), ['view' => 'mine'])) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $view === 'mine' ? 'bg-white text-gray-800' : 'text-white/80 hover:text-white' }}">
                    My Stickers ({{ $myCount }})
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <form method="GET" class="flex flex-col sm:flex-row items-end gap-3">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <div class="flex-1 w-full">
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Sticker ID, Sticker Number, customer name, registration..."
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="w-full sm:w-48">
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-48">
                            <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Company</label>
                            <select name="company_id" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Search
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/40">
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Sticker ID</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Customer</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Vehicle</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Premium</th>
                                <th class="px-5 py-2.5 text-center font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                                <th class="px-5 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                                <th class="px-5 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($stickers as $sticker)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-5 py-2.5 font-mono text-[10px] font-bold text-gray-800 dark:text-white">{{ $sticker->sticker_number }}</td>
                                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->customer_name }}</td>
                                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->car_make }} {{ $sticker->car_model }} ({{ $sticker->car_year }})</td>
                                    <td class="px-5 py-2.5 font-medium text-gray-800 dark:text-white">{{ $sticker->company->name }}</td>
                                    <td class="px-5 py-2.5 text-right font-semibold text-gray-800 dark:text-white">KES {{ number_format($sticker->premium) }}</td>
                                    <td class="px-5 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $sticker->status === 'completed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : ($sticker->status === 'issued' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : ($sticker->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400')) }}">
                                            {{ ucfirst($sticker->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2.5 text-gray-500 dark:text-gray-400">{{ $sticker->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-5 py-2.5 text-right">
                                        <a href="{{ route('stickers.show', $sticker) }}" class="text-[10px] font-medium text-blue-600 hover:text-blue-800">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No stickers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($stickers, 'hasPages') && $stickers->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $stickers->links() }}</div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Reports by Company</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $view === 'mine' ? 'Your stickers' : 'All stickers' }}, grouped by underwriter</p>
                </div>
                <div class="p-5">
                    @if($byCompany->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-4">No data yet.</p>
                    @else
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/40">
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Company</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-gray-400 uppercase tracking-wider text-[10px]">Stickers</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @php $maxCount = $byCompany->max('count'); @endphp
                                @foreach($byCompany as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-white">{{ $item['name'] }}</td>
                                        <td class="px-4 py-2.5">
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="font-semibold text-gray-800 dark:text-white">{{ $item['count'] }}</span>
                                                <div class="w-24 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-full bg-blue-600 rounded-full" style="width:{{ $maxCount > 0 ? ($item['count'] / $maxCount) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- ─── RIGHT SIDEBAR: STICKER LOOKUP ─────────────────────────────── --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-4">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-transparent dark:from-blue-900/10">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Sticker Lookup</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Enter a Sticker Number (from the underwriter) or Sticker ID (ours)</p>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="query" @keyup.enter="search()" placeholder="e.g. STK-001"
                               class="flex-1 min-w-0 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button @click="search()" :disabled="searching || !query.trim()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-50">
                            <svg x-show="!searching" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <svg x-show="searching" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </button>
                    </div>
                    <p x-show="error" x-transition class="mt-2 text-[11px] text-red-600 dark:text-red-400" x-text="error"></p>

                    <div x-show="result" x-transition x-cloak class="mt-4 space-y-3 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="result?.sticker_number"></span>
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                                  :class="result?.is_completed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : (result?.status === 'issued' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400')"
                                  x-text="result?.is_completed ? 'Completed' : (result?.status === 'issued' ? 'Issued' : result?.status)"></span>
                        </div>
                        <div x-show="result?.underwriter_sticker_no" class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Sticker Number</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="result?.underwriter_sticker_no"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Customer</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="result?.customer_name"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Company</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="result?.company"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Vehicle</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="(result?.car_make || '') + ' ' + (result?.car_model || '') + ' (' + (result?.car_year || '') + ')'"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Registration</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="result?.vehicle_registration || 'N/A'"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Premium</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="'KES ' + Number(result?.premium || 0).toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between text-[11px]">
                            <span class="text-gray-400">Issued By</span>
                            <span class="font-semibold text-gray-800 dark:text-white" x-text="result?.issued_by"></span>
                        </div>
                        <a :href="'{{ url('stickers') }}/' + result?.id" class="block text-center mt-2 px-3 py-2 text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                            View Full Sticker
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stickerTrackerLookup() {
    return {
        query: '',
        searching: false,
        error: '',
        result: null,

        async search() {
            if (!this.query.trim()) return;
            this.searching = true;
            this.error = '';
            this.result = null;

            try {
                const response = await fetch('{{ route("admin.stickers.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ search: this.query.trim() }),
                });

                const data = await response.json();
                if (data.success) {
                    this.result = data.sticker;
                } else {
                    this.error = data.error || 'Sticker not found.';
                }
            } catch (e) {
                this.error = 'An error occurred during search.';
            } finally {
                this.searching = false;
            }
        },
    }
}
</script>
@endpush
</x-app-layout>
