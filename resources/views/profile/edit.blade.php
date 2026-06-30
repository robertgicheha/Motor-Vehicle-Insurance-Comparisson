<x-app-layout>
    <div class="space-y-5 mt-10 fade-in">

        {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Account</p>
                    <h1 class="mt-0.5 text-xl font-bold text-white">Profile Settings</h1>
                    <p class="mt-0.5 text-xs text-slate-400">Manage your personal information, security and preferences.</p>
                </div>
            </div>

            {{-- KPI row --}}
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="kpi-card p-4">
                    <div class="accent-bar"></div>
                    <p class="kpi-eyebrow">Member since</p>
                    <p class="kpi-value text-sm mt-1">{{ $user->created_at->format('M Y') }}</p>
                    <span class="kpi-delta neutral mt-1.5 inline-block">Active account</span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#34d399,#6ee7b7)"></div>
                    <p class="kpi-eyebrow">Role</p>
                    <p class="kpi-value text-sm mt-1">{{ ucfirst($user->role ?? 'User') }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">{{ $user->role === 'admin' ? 'Full access' : 'Standard access' }}</span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                    <p class="kpi-eyebrow">Last login</p>
                    <p class="kpi-value text-sm mt-1">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'N/A' }}</p>
                    <span class="kpi-delta neutral mt-1.5 inline-block">Recent session</span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                    <p class="kpi-eyebrow">2FA Status</p>
                    <p class="kpi-value text-sm mt-1">{{ $user->two_factor_confirmed_at ? 'On' : 'Off' }}</p>
                    @if($user->two_factor_confirmed_at)
                        <span class="kpi-delta up mt-1.5 inline-block">Enabled</span>
                    @else
                        <span class="kpi-delta down mt-1.5 text-white inline-block">Not enabled</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ─── PROFILE INFO CARD (read-only display) ───────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Profile information</h2>
                <button onclick="document.getElementById('edit-form').scrollIntoView({behavior:'smooth'})"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                    </svg>
                    Edit
                </button>
            </div>

            {{-- Avatar + name --}}
            <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-gray-600 overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none';this.parentElement.innerHTML='<svg class=\'w-7 h-7 text-blue-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'/></svg>'">
                    @else
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ ucfirst($user->role ?? 'User') }}
                        @if($user->mobile)
                            <span class="mx-1.5 text-gray-300 dark:text-gray-600">|</span>
                            {{ $user->mobile }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Field grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">First name</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->first_name }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Last name</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->last_name }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Email address</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    @if($user->hasVerifiedEmail())
                        <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full border border-green-200 dark:border-green-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Verified
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Not verified
                        </span>
                    @endif
                </div>
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Phone</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->mobile ?? '—' }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Gender</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($user->gender ?? '—') }}</p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Social links</p>
                    <div class="flex items-center gap-3 mt-1">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4l16 16M4 20L20 4"/><rect width="20" height="20" x="2" y="2" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/></svg>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5" stroke-width="2"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── UPDATE PROFILE FORM ──────────────────────────────────────────── --}}
        <div id="edit-form" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Update profile</h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Edit your personal details below</p>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="px-5 py-5 space-y-4">
                @csrf @method('PATCH')

                @if(session('profile_success'))
                    <div class="flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('profile_success') }}
                    </div>
                @endif

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="w-full h-full object-cover" id="avatarPreview" onerror="this.style.display='none'">
                            @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                            <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WebP. Max 2MB.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">First name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Last name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Email address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        @if($user->pending_email)
                            <div class="mt-2 flex items-center gap-2 text-[10px] font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Verification pending for <strong>{{ $user->pending_email }}</strong>. Check your inbox for the verification link.</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Mobile number</label>
                        <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" required
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('mobile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Gender</label>
                        <select name="gender"
                                class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Select gender</option>
                            <option value="male"   {{ $user->gender === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ $user->gender === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ─── SECURITY ─────────────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Security</h2>
            </div>

            {{-- Change Password row --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Change password</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Update your account password</p>
                </div>
                <button onclick="document.getElementById('password-form').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Change password
                </button>
            </div>

            {{-- Password form (toggle) --}}
            <div id="password-form" class="hidden px-5 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30">
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf @method('PATCH')

                    @if(session('password_success'))
                        <div class="flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('password_success') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Current password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">New password</label>
                            <input type="password" name="password" required minlength="8"
                                   class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Confirm password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Update password
                        </button>
                        <button type="button"
                                onclick="document.getElementById('password-form').classList.add('hidden')"
                                class="px-4 py-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2FA row --}}
            <div class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Two-factor authentication (2FA)</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Add an extra layer of security to your account</p>
                </div>
                @if($user->two_factor_confirmed_at)
                    <form action="/user/two-factor-authentication" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            Disable 2FA
                        </button>
                    </form>
                @else
                    <form action="/user/two-factor-authentication" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Enable 2FA
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>