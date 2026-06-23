@extends('layouts.public')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div class="text-[11px] font-bold uppercase tracking-widest text-gray-500 flex items-center gap-2.5">
            Insurance Certificate &mdash; <span class="text-[#0066B3]">{{ $sticker->sticker_number }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('stickers.download', $sticker) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12px] font-bold cursor-pointer border-none transition-all hover:-translate-y-0.5 font-montserrat bg-[#0066B3] text-white hover:bg-[#004f8c]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <a href="{{ route('stickers.print', $sticker) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-[12px] font-bold cursor-pointer border-2 transition-all hover:-translate-y-0.5 font-montserrat bg-white text-[#0066B3] border-[#0066B3] hover:bg-[#e8f2fa]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-3.5 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 px-5 py-3.5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <h4 class="text-[11px] font-bold uppercase tracking-wide text-[#0066B3] mb-3.5 pb-2.5 border-b-2 border-[#e8f2fa]">Certificate Info</h4>
            <div class="space-y-2 text-[13px]">
                <div class="flex justify-between"><span class="text-gray-500">Number</span><span class="font-bold">{{ $sticker->sticker_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Company</span><span class="font-semibold">{{ $sticker->company->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-700">{{ ucfirst($sticker->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Generated</span><span class="font-semibold">{{ $sticker->generated_at?->format('d M Y, h:i A') ?? $sticker->created_at->format('d M Y, h:i A') }}</span></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <h4 class="text-[11px] font-bold uppercase tracking-wide text-[#0066B3] mb-3.5 pb-2.5 border-b-2 border-[#e8f2fa]">Customer</h4>
            <div class="space-y-2 text-[13px]">
                <div class="flex justify-between"><span class="text-gray-500">Name</span><span class="font-bold">{{ $sticker->customer_name }}</span></div>
                @if($sticker->customer_email)<div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-semibold">{{ $sticker->customer_email }}</span></div>@endif
                @if($sticker->customer_phone)<div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-semibold">{{ $sticker->customer_phone }}</span></div>@endif
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <h4 class="text-[11px] font-bold uppercase tracking-wide text-[#0066B3] mb-3.5 pb-2.5 border-b-2 border-[#e8f2fa]">Premium</h4>
            <div class="space-y-2 text-[13px]">
                <div class="flex justify-between"><span class="text-gray-500">Valuation</span><span class="font-bold">KES {{ number_format($sticker->car_valuation, 2) }}</span></div>
                @if($sticker->premium_rate)<div class="flex justify-between"><span class="text-gray-500">Rate</span><span class="font-semibold">{{ $sticker->premium_rate }}%</span></div>@endif
                <div class="flex justify-between"><span class="text-gray-500">Premium</span><span class="font-bold text-lg text-[#0066B3]">KES {{ number_format($sticker->premium, 2) }}</span></div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <h4 class="text-[11px] font-bold uppercase tracking-wide text-[#0066B3] mb-3.5 pb-2.5 border-b-2 border-[#e8f2fa]">Vehicle Details</h4>
        <div class="grid grid-cols-4 gap-4 text-[13px]">
            <div><span class="text-gray-500 block text-[11px]">Make / Model</span><span class="font-semibold">{{ $sticker->car_make }} {{ $sticker->car_model }}</span></div>
            <div><span class="text-gray-500 block text-[11px]">Year</span><span class="font-semibold">{{ $sticker->car_year }}</span></div>
            <div><span class="text-gray-500 block text-[11px]">Registration</span><span class="font-semibold">{{ $sticker->vehicle_registration ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500 block text-[11px]">Cover Type</span><span class="font-semibold">{{ $sticker->cover_type }}</span></div>
        </div>
    </div>

    @if($sticker->breakdown && count($sticker->breakdown) > 0)
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden mt-6 shadow-sm">
            <div class="px-5 py-3.5 text-[11px] font-bold uppercase tracking-wide text-gray-500 border-b border-gray-200">Premium Breakdown</div>
            <table class="w-full border-collapse">
                <thead><tr class="bg-gray-50"><th class="px-5 py-3 text-[11px] font-bold uppercase text-left text-gray-500">Item</th><th class="px-5 py-3 text-[11px] font-bold uppercase text-right text-gray-500">Amount (KES)</th></tr></thead>
                <tbody>
                    @foreach($sticker->breakdown as $key => $value)
                        <tr class="border-b border-gray-200"><td class="px-5 py-3 text-[13px]">{{ ucwords(str_replace('_', ' ', $key)) }}</td><td class="px-5 py-3 text-[13px] font-semibold text-right">{{ is_numeric($value) ? 'KES ' . number_format($value, 2) : $value }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
