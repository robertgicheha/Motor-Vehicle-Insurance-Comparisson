<x-app-layout>
    <div class="space-y-5 mt-10 fade-in">

        {{-- ─── PASSWORD EXPIRY NOTICE ───────────────────────────────────────── --}}
        @if(session('password_expiry_notice') || $user->isPasswordExpired() || $user->isPasswordExpiringSoon())
            @php
                $expired = $user->isPasswordExpired();
                $daysLeft = $user->daysUntilPasswordExpiry();
            @endphp
            <div class="rounded-xl border px-5 py-4 flex items-start gap-3 {{ $expired ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800' }}">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 {{ $expired ? 'text-red-500' : 'text-amber-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm {{ $expired ? 'text-red-700 dark:text-red-300' : 'text-amber-700 dark:text-amber-300' }}">
                    <p class="font-semibold">
                        @if($expired)
                            Your password has expired
                        @else
                            Your password expires soon
                        @endif
                    </p>
                    <p class="mt-0.5">
                        @if($expired)
                            {{ session('password_expiry_notice', 'Your password has expired and must be changed before you can continue.') }}
                        @else
                            Your password will expire {{ $daysLeft === 0 ? 'today' : 'in '.$daysLeft.' day'.($daysLeft === 1 ? '' : 's') }}. Please change it to keep your account secure.
                        @endif
                    </p>
                    <button type="button" onclick="document.getElementById('password-form').classList.remove('hidden'); document.getElementById('password-form').scrollIntoView({behavior:'smooth', block:'center'})"
                            class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold underline">
                        Change password now
                    </button>
                </div>
            </div>
        @endif

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
                    @if($user->avatar && file_exists(public_path($user->avatar)))
                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
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
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Username</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->username }}</p>
                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">
                        Login ID
                    </span>
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
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1">Branch</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->branch ?? '—' }}</p>
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
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 relative">
                            @if($user->avatar && file_exists(public_path($user->avatar)))
                                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" id="avatarPreview">
                            @else
                                <svg class="w-8 h-8 text-gray-400" id="avatarPreviewIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <img src="" alt="" class="w-full h-full object-cover hidden" id="avatarPreview">
                            @endif
                            <div id="avatarCropOverlay" class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 transition-opacity cursor-pointer rounded-full hidden">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                            <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WebP. Max 2MB.</p>
                            <input type="hidden" name="avatar_cropped" id="avatarCropped">
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
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Username</label>
                        <input type="text" value="{{ $user->username }}" readonly
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600/50 text-gray-500 dark:text-gray-300 cursor-not-allowed">
                        <p class="text-[10px] text-gray-400 mt-1">Your username and branch are set by an administrator.</p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Branch</label>
                        <input type="text" value="{{ $user->branch ?? '—' }}" readonly
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600/50 text-gray-500 dark:text-gray-300 cursor-not-allowed">
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
                            <input type="password" name="password" id="newPassword" required minlength="12" autocomplete="new-password"
                                   class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            {{-- Password strength meter --}}
                            <div class="mt-2" id="pwStrengthWrap">
                                <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                    <div id="pwStrengthBar" class="h-full w-0 rounded-full transition-all duration-300" style="background:#ef4444"></div>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p id="pwStrengthLabel" class="text-[10px] font-medium text-gray-400">Password strength</p>
                                    <p class="text-[10px] text-gray-400">Min 12 chars</p>
                                </div>
                                <ul class="mt-2 grid grid-cols-2 gap-1 text-[10px] text-gray-400" id="pwChecklist">
                                    <li data-rule="length" class="flex items-center gap-1"><span class="pw-dot">○</span> 12+ characters</li>
                                    <li data-rule="lower" class="flex items-center gap-1"><span class="pw-dot">○</span> Lowercase letter</li>
                                    <li data-rule="upper" class="flex items-center gap-1"><span class="pw-dot">○</span> Uppercase letter</li>
                                    <li data-rule="number" class="flex items-center gap-1"><span class="pw-dot">○</span> Number</li>
                                    <li data-rule="symbol" class="flex items-center gap-1"><span class="pw-dot">○</span> Symbol</li>
                                </ul>
                            </div>
                            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Confirm password</label>
                            <input type="password" name="password_confirmation" id="confirmPassword" required autocomplete="new-password"
                                   class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p id="pwMatchMsg" class="text-[10px] mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit" id="pwSubmitBtn"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
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

    {{-- ─── CROP MODAL ────────────────────────────────────────────────────── --}}
    <div id="cropModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60" x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Crop Image</h3>
                <button onclick="closeCropModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <div class="w-full max-h-80 overflow-hidden">
                    <img id="cropImage" src="" alt="Crop" class="max-w-full">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                <button onclick="closeCropModal()" class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                <button onclick="applyCrop()" class="px-4 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Apply Crop</button>
            </div>
        </div>
    </div>

<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
let cropper = null;
const avatarInput = document.getElementById('avatarInput');
const cropModal = document.getElementById('cropModal');
const cropImage = document.getElementById('cropImage');
const avatarPreview = document.getElementById('avatarPreview');
const avatarCropped = document.getElementById('avatarCropped');

avatarInput?.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        cropImage.src = e.target.result;
        cropModal.classList.remove('hidden');
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            cropBoxResizable: true,
            cropBoxMovable: true,
        });
    };
    reader.readAsDataURL(file);
});

function closeCropModal() {
    cropModal.classList.add('hidden');
    if (cropper) { cropper.destroy(); cropper = null; }
    avatarInput.value = '';
}

function applyCrop() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'avatar.jpg', { type: 'image/jpeg' });
        const container = new DataTransfer();
        container.items.add(file);
        avatarInput.files = container.files;
        avatarCropped.value = '1';
        avatarPreview.src = URL.createObjectURL(blob);
        avatarPreview.classList.remove('hidden');
        const icon = document.getElementById('avatarPreviewIcon');
        if (icon) icon.classList.add('hidden');
        closeCropModal();
    }, 'image/jpeg', 0.9);
}
</script>

<script>
(function () {
    const pw = document.getElementById('newPassword');
    const confirm = document.getElementById('confirmPassword');
    const bar = document.getElementById('pwStrengthBar');
    const label = document.getElementById('pwStrengthLabel');
    const submit = document.getElementById('pwSubmitBtn');
    const matchMsg = document.getElementById('pwMatchMsg');
    if (!pw || !bar) return;

    const rules = {
        length: v => v.length >= 12,
        lower: v => /[a-z]/.test(v),
        upper: v => /[A-Z]/.test(v),
        number: v => /[0-9]/.test(v),
        symbol: v => /[^A-Za-z0-9]/.test(v),
    };

    const levels = [
        { pct: 0, color: '#ef4444', text: 'Very weak' },
        { pct: 20, color: '#ef4444', text: 'Very weak' },
        { pct: 40, color: '#f59e0b', text: 'Weak' },
        { pct: 60, color: '#eab308', text: 'Fair' },
        { pct: 80, color: '#22c55e', text: 'Strong' },
        { pct: 100, color: '#16a34a', text: 'Very strong' },
    ];

    function evaluate() {
        const v = pw.value;
        let passed = 0;
        document.querySelectorAll('#pwChecklist li').forEach(li => {
            const ok = rules[li.dataset.rule](v);
            if (ok) passed++;
            li.querySelector('.pw-dot').textContent = ok ? '●' : '○';
            li.classList.toggle('text-green-500', ok);
            li.classList.toggle('text-gray-400', !ok);
        });

        const level = levels[passed];
        bar.style.width = level.pct + '%';
        bar.style.background = level.color;
        label.textContent = v ? level.text : 'Password strength';
        label.style.color = v ? level.color : '';

        const strong = passed === 5;
        if (submit) submit.disabled = !strong;
        return strong;
    }

    function checkMatch() {
        if (!confirm || !matchMsg) return;
        if (!confirm.value) { matchMsg.classList.add('hidden'); return; }
        const match = pw.value === confirm.value;
        matchMsg.classList.remove('hidden');
        matchMsg.textContent = match ? 'Passwords match' : 'Passwords do not match';
        matchMsg.classList.toggle('text-green-500', match);
        matchMsg.classList.toggle('text-red-500', !match);
    }

    pw.addEventListener('input', evaluate);
    confirm?.addEventListener('input', checkMatch);
    evaluate();
})();
</script>

</x-app-layout>