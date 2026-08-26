@extends('layouts.public')

@section('content')
    <div class="text-[11px] font-bold uppercase tracking-widest text-gray-500 mb-3.5 flex items-center gap-2.5 after:content-[''] after:flex-1 after:h-px after:bg-gray-200">
        Generate Insurance Certificate
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm mb-8">
        <form method="POST" action="{{ route('stickers.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="quotation_id" value="{{ $quotation->id ?? '' }}">

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Insurance Company <span class="text-red-500">*</span></label>
                    <select name="company_id" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                        <option value="">Select company</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" @selected(old('company_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Cover Type</label>
                    <select name="cover_type"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                        <option value="Comprehensive">Comprehensive</option>
                        <option value="Third Party">Third Party</option>
                        <option value="Third Party Fire & Theft">Third Party Fire & Theft</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Customer Name <span class="text-red-500">*</span></label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="e.g. John Doe"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Customer Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="e.g. john@example.com"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    <p class="text-[11px] text-gray-400 mt-1">Certificate PDF will be emailed if provided</p>
                    @error('customer_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Car Make <span class="text-red-500">*</span></label>
                    <input type="text" name="car_make" value="{{ old('car_make') }}" required placeholder="e.g. Toyota"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('car_make') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Car Model <span class="text-red-500">*</span></label>
                    <input type="text" name="car_model" value="{{ old('car_model') }}" required placeholder="e.g. Camry"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('car_model') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Year <span class="text-red-500">*</span></label>
                    <input type="number" name="car_year" value="{{ old('car_year', date('Y')) }}" required min="1990" max="2030"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('car_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Vehicle Registration</label>
                    <input type="text" name="vehicle_registration" value="{{ old('vehicle_registration') }}" placeholder="e.g. KDH 724V"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Customer Phone</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="e.g. +254 7XX XXX XXX"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Vehicle Valuation (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="car_valuation" value="{{ old('car_valuation') }}" required min="100000" step="0.01"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('car_valuation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Premium (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="premium" value="{{ old('premium') }}" required min="0" step="0.01"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                    @error('premium') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-gray-700 mb-1.5">Premium Rate (%)</label>
                    <input type="number" name="premium_rate" value="{{ old('premium_rate') }}" min="0" max="100" step="0.01" placeholder="e.g. 7.5"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 text-sm font-medium focus:border-[#0066B3] focus:ring-2 focus:ring-[#0066B3]/20 outline-none transition">
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-gray-200">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-xl text-[13px] font-bold cursor-pointer border-none transition-all hover:-translate-y-0.5 font-montserrat bg-[#0066B3] text-white hover:bg-[#004f8c] shadow-sm">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Generate Certificate
                </button>
                <a href="{{ url('/') }}" class="text-[13px] font-semibold text-gray-500 hover:text-gray-700">Cancel</a>
            </div>
        </form>
    </div>
@endsection
