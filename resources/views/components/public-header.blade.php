@inject('settingModel', 'App\Models\Setting')
@php
    $__logoPath = $settingModel->getValue('logo_path', 'images/MIC-Global-Risks_logo.png');
    $__logoUrl = $__logoPath ? (str_starts_with($__logoPath, 'images/') ? asset($__logoPath) : asset('storage/' . $__logoPath)) : asset('images/MIC-Global-Risks_logo.png');
@endphp

<nav class="bg-white border-b border-gray-200 sticky top-0 z-200 shadow-sm">
  <div class="bg-[#004f8c] flex flex-wrap gap-x-5 gap-y-1 justify-end px-4 sm:px-10 py-1.5 text-[11px]">
    <a href="#" class="text-white/75 hover:text-white no-underline">About Us</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Contact Us</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Affiliates</a>
    <a href="#" class="text-white/75 hover:text-white no-underline">Careers</a>
  </div>
  <div class="flex items-center justify-between px-4 sm:px-10 py-3.5">
    <a href="{{ url('/') }}">
        <img src="{{ $__logoUrl }}" alt="MIC Global Risks" class="h-[42px] object-contain">
    </a>

    {{-- Desktop nav --}}
    <div class="hidden md:flex gap-7 items-center">
      <a href="{{ url('/') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Home</a>
      <a href="{{ url('/motor-quote') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Get a Quote</a>
      <a href="{{ url('/motor-private') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Motor Vehicle Private</a>
      <a href="{{ url('/motor-psv') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Motor Vehicle PSV</a>
      <a href="{{ route('stickers.lookup') }}" class="text-[13.5px] font-semibold text-[#1a1a2e] no-underline hover:text-[#0066B3]">Get a Sticker</a>
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

    {{-- Mobile menu toggle --}}
    <button type="button" id="mobile-nav-toggle" class="md:hidden p-2 -mr-2 text-[#1a1a2e]" aria-expanded="false" aria-controls="mobile-nav-menu" aria-label="Open menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>

  {{-- Mobile nav panel --}}
  <div id="mobile-nav-menu" class="hidden md:hidden border-t border-gray-100 px-4 py-3 space-y-1">
    <a href="{{ url('/') }}" class="block py-2 text-[14px] font-semibold text-[#1a1a2e] no-underline">Home</a>
    <a href="{{ url('/motor-quote') }}" class="block py-2 text-[14px] font-semibold text-[#1a1a2e] no-underline">Get a Quote</a>
    <a href="{{ url('/motor-private') }}" class="block py-2 text-[14px] font-semibold text-[#1a1a2e] no-underline">Motor Vehicle Private</a>
    <a href="{{ url('/motor-psv') }}" class="block py-2 text-[14px] font-semibold text-[#1a1a2e] no-underline">Motor Vehicle PSV</a>
    <a href="{{ route('stickers.lookup') }}" class="block py-2 text-[14px] font-semibold text-[#1a1a2e] no-underline">Get a Sticker</a>
    @auth
      <a href="{{ route('dashboard') }}" class="block text-center bg-[#0066B3] text-white text-[14px] font-semibold px-5 py-2.5 rounded-md no-underline mt-2">Dashboard</a>
      <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full bg-[#D0021B] text-white text-[14px] font-semibold px-5 py-2.5 rounded-md no-underline cursor-pointer mt-2">Logout</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="block text-center bg-[#0066B3] text-white text-[14px] font-semibold px-5 py-2.5 rounded-md no-underline mt-2">Login</a>
      <a href="{{ route('register') }}" class="block text-center bg-[#004f8c] text-white text-[14px] font-semibold px-5 py-2.5 rounded-md no-underline mt-2">Sign Up</a>
    @endauth
  </div>
</nav>

<script>
(function () {
  var btn = document.getElementById('mobile-nav-toggle');
  var menu = document.getElementById('mobile-nav-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', function () {
    var isOpen = !menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    btn.setAttribute('aria-expanded', String(!isOpen));
    btn.setAttribute('aria-label', isOpen ? 'Open menu' : 'Close menu');
  });
})();
</script>
