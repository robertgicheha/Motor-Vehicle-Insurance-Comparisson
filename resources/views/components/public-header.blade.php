<nav class="bg-white border-b border-gray-200 sticky top-0 z-200 shadow-sm">
  <div class="bg-[#004f8c] flex gap-5 justify-end px-10 py-1.5 text-[11px]">
    <a href="#" class="text-white/75 hover:text-white no-underline">About Us</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Contact Us</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Affiliates</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Careers</a>
  </div>
  <div class="flex items-center justify-between px-10 py-3.5">
    <a href="{{ url('/') }}">
        <img src="{{ asset('images/MIC-Global-Risks_logo.png') }}" alt="MIC Global Risks" class="h-[42px] object-contain">
    </a>
    <div class="flex gap-7 items-center">
      <a href="{{ url('/') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Home</a>
      <a href="{{ url('/motor-quote') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Get a Quote</a>
      <a href="{{ route('stickers.lookup') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Get a Sticker</a>
      <a href="{{ route('stickers.lookup') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Look Up</a>
      @auth
        <a href="{{ route('dashboard') }}" class="bg-[#0066B3] text-white text-[13.5px] font-semibold px-5 py-2 rounded-md no-underline hover:bg-[#004f8c]">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="bg-[#D0021B] text-white text-[13.5px] font-semibold px-5 py-2 rounded-md no-underline hover:bg-[#a00015] cursor-pointer">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="bg-[#0066B3] text-white text-[13.5px] font-semibold px-5 py-2 rounded-md no-underline hover:bg-[#004f8c]">Login</a>
        <a href="{{ route('register') }}" class="bg-[#004f8c] text-white text-[13.5px] font-semibold px-5 py-2 rounded-md no-underline hover:bg-[#003a6b]">Sign Up</a>
      @endauth
    </div>
  </div>
</nav>
