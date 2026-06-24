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

        <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Quotations</p>

        @can('view quotations')
            <x-sidebar-link href="{{ route('quotations.index') }}" :active="request()->routeIs('quotations.index')" icon="document">
                <span class="flex-1">Quotations</span>
                <span style="font-size:10px; text-white;font-weight:700;background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.7);padding:2px 7px;border-radius:20px;">{{ \App\Models\Quotation::count() }}</span>
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('quotations.create') }}" :active="request()->routeIs('quotations.create')" icon="plus">
                New Quotation
            </x-sidebar-link>
        @endcan

        {{-- <x-sidebar-link href="#" :active="request()->routeIs('stickers*')" icon="ticket">
            <span class="flex-1">Stickers</span>
            @php $pendingStickers = \App\Models\Sticker::where('status','pending')->count(); @endphp
            @if($pendingStickers > 0)
                <span style="font-size:10px;font-weight:700;background:#d4860e;color:#fff;padding:2px 7px;border-radius:20px;">{{ $pendingStickers }}</span>
            @endif
        </x-sidebar-link> --}}

        {{-- <x-sidebar-link href="#" :active="request()->routeIs('email-dispatch*')" icon="mail"> --}}
            {{-- <span class="flex-1">Email Dispatch</span>  --}}
            {{-- @php $failedEmails = \App\Models\EmailLog::where('status','failed')->count(); @endphp --}}
            {{-- @if($failedEmails > 0)
                <span style="font-size:10px;font-weight:700;background:#c0392b;color:#fff;padding:2px 7px;border-radius:20px;">{{ $failedEmails }}</span>
            @endif --}}
         {{-- </x-sidebar-link> --}}

       
         {{-- Divider --}}
        <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>
        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))

            <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Companies</p>
            <x-sidebar-link href="{{ route('admin.companies.index') }}" :active="request()->routeIs('admin.companies*')" icon="building">
                 Companies
            </x-sidebar-link>

        @endif

        {{-- Divider --}}
        <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>



        

        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin'))

            <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Administration</p>

            <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users*')" icon="users">
                Users
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.roles.index') }}" :active="request()->routeIs('admin.roles*')" icon="shield">
                Roles &amp; Permissions
            </x-sidebar-link>

            {{-- <x-sidebar-link href="{{ route('admin.companies.index') }}" :active="request()->routeIs('admin.companies*')" icon="building">
                 Companies
            </x-sidebar-link> --}}

            {{-- Divider --}}
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

            <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">Insights</p>

            <x-sidebar-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports*')" icon="chart">
                Reports
            </x-sidebar-link>

            <x-sidebar-link href="#" :active="request()->routeIs('admin.analytics*')" icon="trending">
                <span class="flex-1">Analytics</span>
                <span style="font-size:10px;font-weight:700;background:#0f7a65;color:#fff;padding:2px 7px;border-radius:20px;">New</span>
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.audit.index') }}" :active="request()->routeIs('admin.audit*')" icon="clock">
                Audit Trail
            </x-sidebar-link>

            {{-- Divider --}}
            <div style="height:0.5px;background:rgba(255,255,255,0.07);margin:8px 10px;"></div>

            <p :class="sidebarOpen ? 'block' : 'hidden'" style="font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.3);padding:6px 10px 5px;">System</p>

            <x-sidebar-link href="#" :active="request()->routeIs('admin.settings.rates*')" icon="adjustments">
                Rate Settings
            </x-sidebar-link>

            <x-sidebar-link href="#" :active="request()->routeIs('admin.notifications*')" icon="bell">
                <span class="flex-1">Notifications</span>
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                @if($unread > 0)
                    <span style="font-size:10px;font-weight:700;background:#c0392b;color:#fff;padding:2px 7px;border-radius:20px;">{{ $unread }}</span>
                @endif
            </x-sidebar-link>

            <x-sidebar-link href="{{ route('admin.settings.index') }}" :active="request()->routeIs('admin.settings.index')" icon="cog">
                Settings
            </x-sidebar-link>

        @endif

        <x-sidebar-link href="#" :active="request()->routeIs('help*')" icon="help">
            Help &amp; Support
        </x-sidebar-link>

    </nav>

    {{-- Footer --}}
    <div class="p-3" style="border-top:0.5px solid rgba(255,255,255,0.08);">
        <div :class="sidebarOpen ? 'block' : 'hidden'">
            {{-- System status --}}
            {{-- <div style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:8px;background:rgba(255,255,255,0.05);margin-bottom:8px;">
                <div style="width:7px;height:7px;border-radius:50%;background:#1a7a45;flex-shrink:0;"></div>
                <span style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.4);letter-spacing:0.4px;">All systems operational</span>
            </div> --}}
            {{-- User --}}
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

        {{-- Collapsed state: avatar only --}}
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