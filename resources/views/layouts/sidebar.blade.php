<aside class="fixed inset-y-0 left-0 z-40 flex flex-col sidebar-transition overflow-x-hidden"
       :class="sidebarOpen ? 'w-64' : 'w-20'"
       style="background:#1a2540; border-right:0.5px solid rgba(255,255,255,0.08); font-family:'Montserrat',sans-serif;"
       x-cloak>

    {{-- Expanded: light mode logo --}}
        <div :class="sidebarOpen ? 'flex items-center gap-2' : 'hidden'" class="dark:hidden">
            <img src="{{ asset('images/MIC-Global-Risks_logo_no_background.png') }}"
                alt="MIC Global Risks"
                class="h-8  w-auto max-w-[140px] ml-5 mt-4 mb-4 object-contain">
        </div>

        {{-- Expanded: dark mode logo (no background) --}}
        <div :class="sidebarOpen ? 'flex items-center gap-2' : 'hidden'" class="hidden dark:flex">
            <img src="{{ asset('images/MIC-Global-Risks_logo_no_background.png') }}"
                alt="MIC Global Risks"
                class="h-8  w-auto max-w-[140px] ml-5 mt-4 mb-4 object-contain">
        </div>

        {{-- Collapsed: light mode logo --}}
        <div :class="!sidebarOpen ? 'flex justify-center w-full' : 'hidden'" class="dark:hidden">
            <img src="{{ asset('images/MIC-Global-Risks_logo_no_background.png') }}"
    alt="MIC Global Risks"
    class="h-7 w-auto max-w-[40px] mt-4 object-contain">
        </div>

        {{-- Collapsed: dark mode logo --}}
        <div :class="!sidebarOpen ? 'flex justify-center w-full' : 'hidden'" class="hidden dark:flex">
           <img src="{{ asset('images/MIC-Global-Risks_logo_no_background.png') }}"
     alt="MIC Global Risks"
    class="h-7 w-auto max-w-[40px] mt-4 object-contain">
        </div>

    {{-- Global Search --}}
    <div :class="sidebarOpen ? 'block px-3 pt-3' : 'hidden'">
        <div onclick="document.dispatchEvent(new CustomEvent('open-search'))"
             style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,0.06);border-radius:9px;padding:8px 10px;border:0.5px solid rgba(255,255,255,0.08);cursor:pointer;">
            <svg style="width:15px;height:15px;color:rgba(255,255,255,0.35)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span style="font-size:11px;color:rgba(255,255,255,0.3);font-weight:500;">Quick search...</span>
            <span style="margin-left:auto;font-size:10px;color:rgba(255,255,255,0.2);font-weight:600;">⌘K</span>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-3 px-2 space-y-0.5">
        {{-- Section: Main --}}
        <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:14px 10px 5px;">Main</p>

        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
            Dashboard
        </x-sidebar-link>

        {{-- Divider --}}
        <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

        {{-- Quotations Dropdown --}}
        <div x-data="{ open: true }">
            <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Quotations</p>
            @can('view quotations')
                <button @click="open = !open"
                        style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.72);font-size:11.5px;font-weight:500;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('quotations*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <span style="opacity:0.7">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak style="flex:1;text-align:left;line-height:1.2">Quotations</span>
                    <span x-show="sidebarOpen" x-cloak style="font-size:10px;background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.7);padding:2px 7px;border-radius:20px;">{{ \App\Models\Quotation::count() }}</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-2 space-y-0.5">
                    <a href="{{ route('quotations.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('quotations.index') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                       hover="background:rgba(255,255,255,0.06);color:#fff;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        View All
                    </a>
                    <a href="{{ route('quotations.tracker') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('quotations.tracker') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                       hover="background:rgba(255,255,255,0.06);color:#fff;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Tracker
                    </a>
                    <a href="{{ route('quotations.create') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('quotations.create') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                       hover="background:rgba(255,255,255,0.06);color:#fff;">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Quotation
                    </a>
                </div>
            @endcan
        </div>

        {{-- Divider --}}
        <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))
            {{-- Companies Dropdown --}}
            <div x-data="{ open: true }">
                <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Companies</p>
                <button @click="open = !open"
                        style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.72);font-size:11.5px;font-weight:500;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('admin.companies*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <span style="opacity:0.7">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak style="flex:1;text-align:left;line-height:1.2">Companies</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-2 space-y-0.5">
                    <a href="{{ route('admin.companies.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.companies.index') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        View All
                    </a>
                    <a href="{{ route('admin.companies.create') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.companies.create') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Company
                    </a>
                    <a href="{{ route('admin.compare-all.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.compare-all*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Compare All Card
                    </a>
                </div>
            </div>

            {{-- Divider --}}
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

            {{-- Administration Dropdown --}}
            <div x-data="{ open: true }">
                <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Administration</p>
                <button @click="open = !open"
                        style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.72);font-size:11.5px;font-weight:500;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('admin.users*') || request()->routeIs('admin.roles*') || request()->routeIs('admin.stickers*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <span style="opacity:0.7">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak style="flex:1;text-align:left;line-height:1.2">Administration</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-2 space-y-0.5">
                    <div x-data="{ subOpen: true }">
                        <button @click="subOpen = !subOpen"
                                style="display:flex;align-items:center;gap:8px;width:100%;padding:6px 12px;border-radius:8px;border:none;background:transparent;color:rgba(255,255,255,0.6);font-size:11px;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('admin.users*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                            <span style="flex:1;text-align:left">Users</span>
                            <svg class="w-2.5 h-2.5 transition-transform" :class="subOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-cloak class="ml-3 space-y-0.5">
                            <a href="{{ route('admin.users.index') }}" style="display:flex;align-items:center;gap:8px;padding:5px 12px;border-radius:6px;font-size:10.5px;color:rgba(255,255,255,0.5);text-decoration:none;">View All</a>
                            <a href="{{ route('admin.users.create') }}" style="display:flex;align-items:center;gap:8px;padding:5px 12px;border-radius:6px;font-size:10.5px;color:rgba(255,255,255,0.5);text-decoration:none;">Add User</a>
                        </div>
                    </div>
                    <a href="{{ route('admin.roles.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.roles*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Roles &amp; Permissions
                    </a>
                    <a href="{{ route('admin.stickers.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.stickers*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Certificates
                    </a>
                </div>
            </div>

            {{-- Divider --}}
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

            {{-- Insights Dropdown --}}
            <div x-data="{ open: true }">
                <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Insights</p>
                <button @click="open = !open"
                        style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.72);font-size:11.5px;font-weight:500;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('admin.reports*') || request()->routeIs('admin.analytics*') || request()->routeIs('admin.audit*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <span style="opacity:0.7">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak style="flex:1;text-align:left;line-height:1.2">Insights</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-2 space-y-0.5">
                    <a href="{{ route('admin.reports.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.reports*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Reports
                    </a>
                    <a href="{{ route('admin.analytics.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.analytics*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Analytics
                        <span style="font-size:9px;font-weight:700;background:#0f7a65;color:#fff;padding:1px 6px;border-radius:20px;">New</span>
                    </a>
                    <a href="{{ route('admin.audit.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.audit*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Audit Trail
                    </a>
                </div>
            </div>

            {{-- Divider --}}
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

            {{-- System Dropdown --}}
            <div x-data="{ open: true }">
                <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">System</p>
                <button @click="open = !open"
                        style="display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;border-radius:9px;border:none;background:transparent;color:rgba(255,255,255,0.72);font-size:11.5px;font-weight:500;cursor:pointer;font-family:'Montserrat',sans-serif;{{ request()->routeIs('admin.settings*') || request()->routeIs('admin.notifications*') || request()->routeIs('admin.rate-settings*') || request()->routeIs('admin.help*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}"
                        :class="sidebarOpen ? '' : 'justify-center'">
                    <span style="opacity:0.7">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <span x-show="sidebarOpen" x-cloak style="flex:1;text-align:left;line-height:1.2">System</span>
                    <svg x-show="sidebarOpen" x-cloak class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-2 space-y-0.5">
                    <a href="{{ route('admin.rate-settings.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.rate-settings*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Rate Settings
                    </a>
                    <a href="{{ route('admin.notifications.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.notifications*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Notifications
                        @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                        @if($unread > 0)
                            <span style="font-size:9px;font-weight:700;background:#c0392b;color:#fff;padding:1px 6px;border-radius:20px;">{{ $unread }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.settings.index') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </a>
                    <a href="{{ route('admin.help.index') }}"
                       style="display:flex;align-items:center;gap:8px;padding:6px 12px;border-radius:8px;font-size:11px;color:rgba(255,255,255,0.6);text-decoration:none;{{ request()->routeIs('admin.help*') ? 'background:rgba(0,95,158,0.18);color:#fff;' : '' }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Help &amp; Support
                    </a>
                </div>
            </div>
        @endif

        {{-- Non-admin links --}}
        @if(!auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin'))
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>
            @php
                $latestQuote = auth()->user()->quotations()->with('company')->latest()->first();
                $viewQuoteUrl = $latestQuote && $latestQuote->company ? '/quotation/' . $latestQuote->company->slug : route('quotations.index');
            @endphp
            <x-sidebar-link href="{{ $viewQuoteUrl }}" :active="request()->routeIs('motor.quotation')" icon="document">
                View Quote
            </x-sidebar-link>
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>
            <x-sidebar-link href="{{ route('stickers.user-lookup') }}" :active="request()->routeIs('stickers.user-lookup')" icon="ticket">
                Certificates / Stickers
            </x-sidebar-link>
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>
            <x-sidebar-link href="{{ route('user.help.index') }}" :active="request()->routeIs('user.help*')" icon="help">
                Help &amp; Support
            </x-sidebar-link>
        @endif

    </nav>

    {{-- Footer --}}
    <div class="p-3" style="border-top:0.5px solid rgba(255,255,255,0.08);">
        <div :class="sidebarOpen ? 'block' : 'hidden'">
            <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:9px;background:rgba(255,255,255,0.06);">
                <div style="width:32px;height:32px;border-radius:50%;background:#1756c8;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:12px;font-weight:700;color:#fff;margin:0;truncate;">{{ auth()->user()->name }}</p>
                    <p style="font-size:10px;color:rgba(255,255,255,0.4);margin:0;font-weight:500;">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</p>
                </div>
                <div style="display:flex;gap:4px;">
                    <a href="{{ route('profile.edit') }}" style="width:26px;height:26px;border-radius:6px;background:rgba(255,255,255,0.07);display:flex;align-items:center;justify-content:center;" title="Profile">
                        <svg style="width:13px;height:13px;color:rgba(255,255,255,0.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="width:26px;height:26px;border-radius:6px;background:rgba(255,255,255,0.07);display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;" title="Log out">
                            <svg style="width:13px;height:13px;color:rgba(255,255,255,0.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div :class="!sidebarOpen ? 'flex flex-col items-center gap-3' : 'hidden'">
            <div style="width:32px;height:32px;border-radius:50%;background:#1756c8;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.07);display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;" title="Log out">
                    <svg style="width:14px;height:14px;color:rgba(255,255,255,0.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
