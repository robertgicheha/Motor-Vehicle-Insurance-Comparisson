<x-app-layout>
<div class="space-y-6 pt-10 fade-in">

    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Support</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Enquiry Details</h1>
                <p class="mt-0.5 text-xs text-slate-400">View and respond to support requests</p>
            </div>
            <a href="{{ route('admin.help.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Message</h2>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-gray-800 dark:text-white whitespace-pre-wrap">{{ $enquiry->message }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Send Response</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">This will notify the user via their dashboard</p>
                </div>
                <div class="px-5 py-4">
                    <form action="{{ route('admin.help.respond', $enquiry) }}" method="POST">
                        @csrf
                        <textarea name="response" rows="5" required
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 outline-none"
                                  placeholder="Type your response here..."></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Send Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Details</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</p>
                        <p class="text-xs font-medium text-gray-800 dark:text-white mt-0.5">{{ $enquiry->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</p>
                        <p class="text-xs text-gray-800 dark:text-white mt-0.5">{{ $enquiry->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</p>
                        <p class="text-xs font-medium text-gray-800 dark:text-white mt-0.5">{{ $enquiry->subject }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium mt-1
                            {{ $enquiry->status === 'open' ? 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' : '' }}
                            {{ $enquiry->status === 'in_progress' ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                            {{ $enquiry->status === 'resolved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $enquiry->status)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Submitted</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $enquiry->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
