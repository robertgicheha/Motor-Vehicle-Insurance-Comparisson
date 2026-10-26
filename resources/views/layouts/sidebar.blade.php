@php
    $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
    $isSuperAdmin = auth()->user()->hasRole('super-admin');

    $currentRoute = request()->route()?->getName() ?? '';
    $isActive = fn(string $pattern) => request()->routeIs($pattern);

    $lastViewed = session('quotations_last_viewed_at');
    $sidebarCounts = \Illuminate\Support\Facades\Cache::remember(
        'sidebar_counts_' . auth()->id() . '_' . ($lastViewed ?: 'none'),
        30,
        function () use ($lastViewed, $isAdmin) {
            return [
                'quotes' => $lastViewed
                    ? \App\Models\Quotation::where('created_at', '>', $lastViewed)->count()
                    : \App\Models\Quotation::count(),
                'claims' => \App\Models\Claim::where(function ($q) {
                    $q->where('user_id', auth()->id())->orWhere('on_behalf_of_user_id', auth()->id());
                })->count(),
                'pendingStickers' => $isAdmin ? \App\Models\Sticker::where('status', 'issued')->count() : 0,
            ];
        }
    );
    $quoteCount = $sidebarCounts['quotes'];
    $claimCount = $sidebarCounts['claims'];
    $pendingStickerCount = $sidebarCounts['pendingStickers'];

    // Shared style strings so every toggle button behaves identically
    $activeStyle   = 'background:rgba(255,255,255,0.16); color:#ffffff; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.18);';
    $inactiveStyle = 'color:#ffffff;';
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex flex-col sidebar-transition overflow-x-hidden bg-[#263B8B] border-r border-white/[0.08] shadow-[4px_0_24px_rgba(0,0,0,0.12)]"
    :class="sidebarOpen ? 'w-60' : 'w-[68px]'"
    x-data="{
        open: {},
        isMobile: window.innerWidth < 1024,
        init() {
            this.isMobile = window.innerWidth < 1024;
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
                if (!this.isMobile) this.mobileSidebarOpen = false;
            });
            @if($isAdmin && ($isActive('quotations*') || $isActive('my.quotations') || $isActive('recent.quotations')))
                this.open.quotations = true;
            @endif
            @if($isAdmin && ($isActive('claims*') || $isActive('claims.mine')))
                this.open.claims = true;
            @endif
            @if($isAdmin && ($isActive('admin.companies*') || $isActive('admin.compare-all*')))
                this.open.companies = true;
            @endif
            @if($isAdmin && ($isActive('admin.users*') || $isActive('admin.roles*') || $isActive('admin.stickers*')))
                this.open.users = true;
            @endif
            @if($isAdmin && ($isActive('stickers.issue*') || $isActive('stickers.*') || $isActive('admin.stickers*') || $isActive('stickers.user-lookup')))
                this.open.certificates = true;
            @endif
            @if($isAdmin && ($isActive('admin.reports*') || $isActive('admin.analytics*') || $isActive('admin.audit*') || $isActive('admin.email-analytics*')))
                this.open.insights = true;
            @endif
            @if($isSuperAdmin && ($isActive('admin.settings*') || $isActive('admin.notifications*') || $isActive('admin.rate-settings*') || $isActive('admin.help*') || $isActive('admin.media*') || $isActive('admin.health*') || $isActive('admin.email-settings*') || $isActive('admin.email-config*')))
                this.open.system = true;
            @endif
            @if($isAdmin && !$isSuperAdmin && ($isActive('admin.settings*') || $isActive('admin.help*')))
                this.open.settings = true;
            @endif
        },
        toggle(key) {
            if (!this.sidebarOpen) {
                window.dispatchEvent(new CustomEvent('sidebar-expand'));
                this.$nextTick(() => { this.open[key] = true; });
            } else {
                this.open[key] = !this.open[key];
            }
        },
        isOpen(key) { return this.open[key] || false; }
    }"
    :style="{ transform: isMobile ? (mobileSidebarOpen ? 'translateX(0)' : 'translateX(-100%)') : 'none' }"
    x-cloak
>
    {{-- Logo --}}
    <div class="flex items-center shrink-0" :class="sidebarOpen ? 'px-4 py-4' : 'px-0 py-4 justify-center'">
        <a href="{{ route('dashboard') }}" class="block" :class="sidebarOpen ? '' : 'flex justify-center'">
            <img src="{{ asset('images/LOLC-Kenya.webp') }}"
                 alt="LOLC Microfinance Bank"
                 class="object-contain transition-all duration-300"
                 :class="sidebarOpen ? 'h-8 w-auto max-w-[140px]' : 'h-7 w-auto max-w-[36px]'" width="417" height="67">
        </a>
    </div>

    {{-- Quick Search --}}
    <div x-show="sidebarOpen" x-cloak class="px-3 pb-2">
        <button onclick="document.dispatchEvent(new CustomEvent('open-search'))"
                class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs text-white hover:bg-white/[0.10] transition-all duration-150"
                style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12);">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="flex-1 text-left text-white/80">Quick search...</span>
            <kbd class="hidden lg:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold text-white bg-white/[0.14] rounded">⌘K</kbd>
        </button>
    </div>

    {{-- Scrollable Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2.5 pb-3 scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent"
         @click="if (isMobile && $event.target.closest('a')) { window.dispatchEvent(new CustomEvent('sidebar-close-mobile')); }">

        {{-- ─── Main ─── --}}
        <div class="pt-1 pb-1">
            <p x-show="sidebarOpen" x-cloak class="px-3 pt-3 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                Main
            </p>
            <x-sidebar-link href="{{ route('dashboard') }}" :active="$isActive('dashboard')" icon="dashboard">
                Dashboard
            </x-sidebar-link>
        </div>

        <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>

        {{-- ─── Quotations ─── --}}
        <div class="py-1">
            <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                Quotations
            </p>
            @can('view quotations')
                <a href="{{ route('quotations.index') }}" @click.prevent.stop="toggle('quotations')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('quotations*') || $isActive('my.quotations') || $isActive('recent.quotations')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-white text-left leading-tight">Quotations</span>
                    <span x-show="sidebarOpen" x-cloak
                          class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-white/[0.18] text-white">{{ $quoteCount }}</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('quotations') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('quotations') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('quotations.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('quotations.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        View All
                    </a>
                    <a href="{{ route('my.quotations') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('my.quotations') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        My Quotations
                    </a>
                    <a href="{{ route('recent.quotations') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('recent.quotations') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Recent
                    </a>
                    <a href="{{ route('quotations.tracker') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('quotations.tracker') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Tracker
                    </a>
                    <a href="{{ route('quotations.create') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('quotations.create') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        <span class="w-3 h-3 flex items-center justify-center rounded-full border border-white/40 text-[9px] leading-none">+</span>
                        New Quotation
                    </a>
                </div>
            @endcan
        </div>

        {{-- ─── Claims ─── --}}
        <div class="py-1">
            <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                Claims
            </p>
            <a href="{{ $isAdmin ? route('admin.claims.index') : route('claims.index') }}" @click.prevent.stop="toggle('claims')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                    :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                    style="{{ ($isActive('claims*') || $isActive('claims.mine')) ? $activeStyle : $inactiveStyle }}">
                <span class="shrink-0 text-white transition-opacity">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Claims</span>
                <span x-show="sidebarOpen" x-cloak
                      class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-white/[0.18] text-white">{{ $claimCount }}</span>
                <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('claims') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </a>
                <div x-show="isOpen('claims') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                <a href="{{ route('claims.create') }}"
                   class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                          {{ $isActive('claims.create') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                    Log New Claim
                </a>
                @if($isAdmin)
                    <a href="{{ route('admin.claims.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.claims.index') || $isActive('admin.claims.show') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        All Claims
                    </a>
                    <a href="{{ route('admin.claims.reports') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.claims.reports') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Claim Reports
                    </a>
                @else
                    <a href="{{ route('claims.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('claims.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        All Claims
                    </a>
                @endif
                {{-- <a href="{{ route('claims.mine') }}"
                   class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                          {{ $isActive('claims.mine') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                    My Claims
                </a> --}}
            </div>
        </div>

        @if($isAdmin)
            @if($isSuperAdmin)
            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>

            {{-- ─── Underwriters ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Underwriters
                </p>
                <a href="{{ route('admin.companies.index') }}" @click.prevent.stop="toggle('companies')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('admin.companies*') || $isActive('admin.compare-all*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Underwriters</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('companies') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('companies') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('admin.companies.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.companies.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        View All
                    </a>
                    <a href="{{ route('admin.companies.create') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.companies.create') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Add Underwriter
                    </a>
                    <a href="{{ route('admin.compare-all.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.compare-all*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Compare All
                    </a>
                </div>
            </div>
            @endif

            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>

            {{-- ─── Administration ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Administration
                </p>

                <a href="{{ route('admin.users.index') }}" @click.prevent.stop="toggle('users')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('admin.users*') || $isActive('admin.roles*') || $isActive('admin.stickers*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Users & Roles</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('users') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('users') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('admin.users.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.users.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        View All Users
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.users.create') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Add User
                    </a>
                    <a href="{{ route('admin.roles.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.roles*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Roles & Permissions
                    </a>
                    
                </div>
            </div>

            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>

            {{-- ─── Insurance Sticker ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Sticker Issuance
                </p>

                <a href="{{ route('stickers.issue') }}" @click.prevent.stop="toggle('certificates')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('stickers.issue*') || $isActive('stickers.*') || $isActive('admin.stickers*') || $isActive('stickers.user-lookup')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Stickers</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('certificates') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('certificates') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                     <a href="{{ route('stickers.issue') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.issue') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Create New Sticker
                    </a>

                     <a href="{{ route('admin.stickers.update-form') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.stickers.update-form') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Update Stickers
                        @if($pendingStickerCount > 0)
                            <span class="ml-auto text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-400 text-amber-900">{{ $pendingStickerCount }}</span>
                        @endif
                    </a>

                    <a href="{{ route('admin.stickers.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.stickers.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        View All Stickers
                    </a>
                    <a href="{{ route('stickers.user-lookup') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.user-lookup') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Sticker Tracker
                    </a>

                     
                    {{-- <a href="{{ route('admin.stickers.email-settings') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.stickers.email-settings*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Email Settings
                    </a> --}}
                    {{-- <a href="{{ route('admin.stickers.analytics') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.stickers.analytics') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Reports & Analytics
                    </a> --}}
                </div>
            </div>

          <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>


            {{-- ─── Insights ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Insights
                </p>
                <a href="{{ route('admin.reports.index') }}" @click.prevent.stop="toggle('insights')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('admin.reports*') || $isActive('admin.analytics*') || $isActive('admin.audit*') || $isActive('admin.email-analytics*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Insights</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('insights') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('insights') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('admin.reports.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.reports*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Reports
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.analytics*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Analytics
                        <span class="ml-1 text-[9px] font-bold px-1.5 py-px rounded-full bg-emerald-400 text-[#263B8B]">New</span>
                    </a>
                    <a href="{{ route('admin.audit.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.audit.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Audit Trail
                    </a>
                    <a href="{{ route('admin.audit.activity') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.audit.activity') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Activity Logs
                    </a>
                    <a href="{{ route('admin.audit.story') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.audit.story') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        User Story
                    </a>
                    <a href="{{ route('admin.email-analytics.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.email-analytics*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Email Analytics
                    </a>
                </div>
            </div>

            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>

            @if($isSuperAdmin)
            {{-- ─── System ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    System
                </p>
                <a href="{{ route('admin.rate-settings.index') }}" @click.prevent.stop="toggle('system')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('admin.settings*') || $isActive('admin.notifications*') || $isActive('admin.rate-settings*') || $isActive('admin.help*') || $isActive('admin.media*') || $isActive('admin.health*') || $isActive('admin.email-settings*') || $isActive('admin.email-config*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">System</span>
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if($unread > 0)
                        <span x-show="sidebarOpen" x-cloak
                              class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-red-500 text-white">{{ $unread }}</span>
                    @endif
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('system') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('system') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('admin.rate-settings.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.rate-settings*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Rate Settings
                    </a>
                    <a href="{{ route('admin.email-settings.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.email-settings*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Email Templates
                    </a>
                    <a href="{{ route('admin.email-config.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.email-config*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Email Settings
                    </a>
                    <a href="{{ route('admin.notifications.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.notifications*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Notifications
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.settings.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Settings
                    </a>
                    <a href="{{ route('admin.help.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.help*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Help & Support
                    </a>

                    <a href="{{ route('admin.stickers.settings') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.stickers.settings') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Sticker Settings
                    </a>

                    <a href="{{ route('admin.media.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.media*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Media Library
                    </a>
                    <a href="{{ route('admin.health.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.health*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Platform Health
                    </a>
                </div>
            </div>
            @else
            {{-- ─── Settings (admin role — trimmed) ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Settings
                </p>
                <a href="{{ route('admin.settings.index') }}" @click.prevent.stop="toggle('settings')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('admin.settings*') || $isActive('admin.help*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Settings</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('settings') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('settings') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                    <a href="{{ route('admin.settings.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.settings.index') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Settings
                    </a>
                    <a href="{{ route('admin.help.index') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('admin.help*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Help & Support
                    </a>
                </div>
            </div>
            @endif
        @endif

        {{-- ─── Non-admin Links ─── --}}
        @if(!$isAdmin)
            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>
            <div class="py-1">
                  <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                  My Quotes
                </p>
                <a href="{{ route('my.quote') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                   :class="sidebarOpen ? '' : 'justify-center'"
                   style="{{ (request()->routeIs('motor.quotation') || request()->routeIs('my.quote')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="text-white leading-tight">View Quote</span>
                </a>
                  {{-- ─── Insurance Sticker ─── --}}
            <div class="py-1">
                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                    Sticker Issuance
                </p>

                <a href="{{ route('stickers.issue') }}" @click.prevent.stop="toggle('certificates')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                        :class="sidebarOpen ? 'justify-start' : 'justify-center'"
                        style="{{ ($isActive('stickers.issue*') || $isActive('stickers.my-stickers*') || $isActive('stickers.my-updates*') || $isActive('stickers.user-lookup')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="flex-1 text-left text-white leading-tight">Stickers</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 shrink-0 text-white transition-transform duration-200" :class="isOpen('certificates') ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div x-show="isOpen('certificates') && sidebarOpen" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-1"
                     class="mt-0.5 space-y-0.5">
                     <a href="{{ route('stickers.issue') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.issue') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Create New Sticker
                    </a>

                     <a href="{{ route('stickers.my-updates') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.my-updates*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Update Stickers
                    </a>

                    <a href="{{ route('stickers.my-stickers') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.my-stickers*') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        View All Stickers
                    </a>
                    <a href="{{ route('stickers.user-lookup') }}"
                       class="sidebar-sub-link flex items-center gap-2 pl-[42px] pr-3 py-1.5 rounded-lg text-[12px] text-white transition-all duration-150
                              {{ $isActive('stickers.user-lookup') ? 'bg-white/[0.16]' : 'hover:bg-white/[0.10]' }}">
                        Sticker Tracker
                    </a>
                </div>
            </div>

            <div class="mx-3 my-1 h-px bg-white/[0.12]"></div>


                <p x-show="sidebarOpen" x-cloak class="px-3 pt-2 pb-1.5 text-[9px] font-bold uppercase tracking-[0.15em] text-white/70">
                Settings
                </p>

             <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                   :class="sidebarOpen ? '' : 'justify-center'"
                   style="{{ ($isActive('profile.edit')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="text-white leading-tight">
                        Settings
                    </span>
                </a>

                <a href="{{ route('user.help.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[13px] font-medium transition-all duration-150 group"
                   :class="sidebarOpen ? '' : 'justify-center'"
                   style="{{ ($isActive('user.help*')) ? $activeStyle : $inactiveStyle }}">
                    <span class="shrink-0 text-white transition-opacity">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak class="text-white leading-tight">Help & Support</span>
                </a>
            </div>
        @endif
    </nav>

    {{-- ─── Footer / User Card ─── --}}
    <div class="shrink-0 border-t border-white/[0.12] p-2.5">
        {{-- Expanded --}}
        <div x-show="sidebarOpen" x-cloak>
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl" style="background:rgba(255,255,255,0.08);">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-[#263B8B] shrink-0" style="background:#ffffff;">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12px] font-semibold text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-white/75 font-medium leading-tight">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('profile.edit') }}"
                       class="w-7 h-7 flex items-center justify-center rounded-lg text-white hover:bg-white/[0.14] transition-all"
                       title="Profile">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-white hover:bg-red-500/80 transition-all"
                                title="Sign out">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Collapsed --}}
        <div x-show="!sidebarOpen" x-cloak class="flex flex-col items-center gap-2 py-1">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-[#263B8B]" style="background:#ffffff;">
                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-white hover:bg-red-500/80 transition-all"
                        title="Sign out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>