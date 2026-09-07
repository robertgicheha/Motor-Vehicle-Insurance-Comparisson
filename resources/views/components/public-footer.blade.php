@inject('settingModel', 'App\Models\Setting')
@php
    $__footerLogo = $settingModel->getValue('logo_path', 'images/MIC-Global-Risks_logo_no_background.png');
    $__footerLogoUrl = $__footerLogo ? (str_starts_with($__footerLogo, 'images/') ? asset($__footerLogo) : asset('storage/' . $__footerLogo)) : asset('images/MIC-Global-Risks_logo_no_background.png');
@endphp

<footer class="bg-[#004f8c] text-white mt-auto">
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-[1.5fr_1fr_1fr_1fr] gap-8 md:gap-10 px-6 sm:px-10 py-10 md:py-[50px] pb-9 max-w-[1200px] mx-auto">
    <div>
      <img src="{{ $__footerLogoUrl }}" alt="MIC Global Risks" class="h-10 object-contain mb-4 brightness-0 invert">
      <p class="text-[12.5px] text-white/60 leading-relaxed">Your trusted insurance intermediary &mdash; connecting you to Kenya&rsquo;s leading insurers with transparency, speed and expertise.</p>
      <div class="flex gap-2 flex-wrap mt-3">
        <span class="bg-white/10 border border-white/20 rounded-full px-3 py-1 text-[11px] font-bold text-white/85">Kenya</span>
        <span class="bg-white/10 border border-white/20 rounded-full px-3 py-1 text-[11px] font-bold text-white/85">Uganda</span>
        <span class="bg-white/10 border border-white/20 rounded-full px-3 py-1 text-[11px] font-bold text-white/85">Tanzania</span>
      </div>
    </div>
    <div>
      <h5 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-3.5">Company</h5>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">About Us</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Our Team</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Careers</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Affiliates</a>
    </div>
    <div>
      <h5 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-3.5">Services</h5>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Motor Insurance</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Property Cover</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Travel Insurance</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Medical Cover</a>
    </div>
    <div>
      <h5 class="text-[11px] font-bold uppercase tracking-widest text-white/50 mb-3.5">Contact</h5>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">6th Floor, 9 West</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Ring Road Parklands</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">Nairobi, Kenya</a>
      <a href="#" class="block text-[13px] text-white/75 no-underline py-2 hover:text-white">www.micglobalrisks.com</a>
    </div>
  </div>
  <div class="border-t border-white/10 px-6 sm:px-10 py-[18px] max-w-[1200px] mx-auto flex flex-col sm:flex-row gap-2 sm:gap-0 justify-between items-center text-[12px] text-white/50 text-center sm:text-left">
    <span>&copy; 2026 MIC Global Risks Insurance (Kenya) Limited</span>
    <div>
      <a href="#" class="text-white/60 no-underline ml-0 sm:ml-5 mr-2.5 sm:mr-0 hover:text-white py-1.5 inline-block">Privacy Policy</a>
      <a href="#" class="text-white/60 no-underline ml-0 sm:ml-5 mr-2.5 sm:mr-0 hover:text-white py-1.5 inline-block">Terms &amp; Conditions</a>
      <a href="#" class="text-white/60 no-underline ml-0 sm:ml-5 hover:text-white py-1.5 inline-block">Contact Us</a>
    </div>
  </div>
</footer>
