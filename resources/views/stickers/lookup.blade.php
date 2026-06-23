@extends('layouts.public')

@section('title', 'Look Up Certificate')

@section('content')
    <div class="max-w-lg mx-auto">
        <div class="text-[11px] font-bold uppercase tracking-widest text-gray-500 mb-3.5 flex items-center gap-2.5 after:content-[''] after:flex-1 after:h-px after:bg-gray-200">
            Look Up Insurance Certificate
        </div>

        @if(session('error'))
            <div class="mb-6 px-5 py-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <div class="text-center mb-6">
                <svg class="w-16 h-16 mx-auto text-[#0066B3] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h2 class="text-lg font-bold text-gray-900">Enter Certificate Number</h2>
                <p class="text-sm text-gray-500 mt-1">Enter the certificate number (e.g. STK202606230001) to view and download your certificate.</p>
            </div>

            <form method="POST" action="{{ route('stickers.search') }}" class="space-y-4">
                @csrf
                <div>
                    <input type="text" name="number" required placeholder="e.g. STK202606230001"
                           class="w-full px-5 py-3.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-center focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition text-lg"
                           style="text-transform:uppercase">
                </div>
                <button type="submit"
                        class="w-full py-3.5 rounded-xl text-[14px] font-bold cursor-pointer border-none transition-all font-montserrat bg-[#0066B3] text-white hover:bg-[#004f8c] shadow-sm">
                    Look Up Certificate
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('stickers.create') }}" class="text-[13px] font-semibold text-[#0066B3] hover:text-[#004f8c]">
                Don't have a certificate? Generate one &rarr;
            </a>
        </div>
    </div>
@endsection
