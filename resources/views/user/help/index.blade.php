<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Support</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Help & Support</h1>
                <p class="mt-0.5 text-xs text-slate-400">Submit an enquiry or browse our help resources</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Submit an Enquiry</h2>
                <p class="text-[10px] text-gray-400 mt-0.5">We'll get back to you as soon as possible</p>
            </div>
            <form action="{{ route('user.help.store') }}" method="POST" class="px-5 py-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Subject <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" required placeholder="e.g. How do I generate a quotation?"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Message <span class="text-rose-500">*</span></label>
                    <textarea name="message" rows="5" required placeholder="Describe your issue or question..."
                              class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Submit Enquiry
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Your Enquiries</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Track your submitted requests</p>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($enquiries as $enquiry)
                        <div class="px-5 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-medium text-gray-800 dark:text-white">{{ $enquiry->subject }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-2">{{ $enquiry->message }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1">{{ $enquiry->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium shrink-0
                                    {{ $enquiry->status === 'open' ? 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : '' }}
                                    {{ $enquiry->status === 'in_progress' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                    {{ $enquiry->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-xs text-gray-400">No enquiries yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Quick Help</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Use <strong>New Quotation</strong> to compare insurance premiums across multiple companies.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Download your certificate/sticker from the quotation detail page once issued.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-gray-600 dark:text-gray-300">Need a new quotation or sticker for an existing customer? Use the <strong>Generate Sticker</strong> feature.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
