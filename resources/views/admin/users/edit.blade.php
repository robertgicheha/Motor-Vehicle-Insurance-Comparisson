<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-5 mt-10 fade-in">

        {{-- ─── HERO ─────────────────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">User Management</p>
                    <h1 class="mt-0.5 text-xl font-bold text-white">Edit User</h1>
                    <p class="mt-0.5 text-xs text-slate-400">Update {{ $user->username }}'s account information and access.</p>
                </div>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-white/80 hover:text-white border border-white/20 hover:bg-white/10 rounded-lg transition shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Users
                </a>
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
                    <p class="kpi-eyebrow">Current role</p>
                    <p class="kpi-value text-sm mt-1">{{ ucfirst($user->getRoleNames()->first() ?? 'User') }}</p>
                    <span class="kpi-delta up mt-1.5 inline-block">Assigned</span>
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#818cf8,#a5b4fc)"></div>
                    <p class="kpi-eyebrow">Status</p>
                    <p class="kpi-value text-sm mt-1">{{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                    @if($user->is_active)
                        <span class="kpi-delta up mt-1.5 inline-block">Can sign in</span>
                    @else
                        <span class="kpi-delta neutral mt-1.5 inline-block">Access disabled</span>
                    @endif
                </div>

                <div class="kpi-card p-4">
                    <div class="accent-bar" style="background:linear-gradient(90deg,#fb7185,#fda4af)"></div>
                    <p class="kpi-eyebrow">Username</p>
                    <p class="kpi-value text-sm mt-1 font-mono">{{ $user->username }}</p>
                    <span class="kpi-delta neutral mt-1.5 inline-block">Login ID</span>
                </div>
            </div>
        </div>

        {{-- ─── EDIT USER FORM ───────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 border border-gray-200 dark:border-gray-600">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Account details</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Edit this user's role and access</p>
                </div>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="px-5 py-5 space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Branch</label>
                        <input type="text" name="branch" value="{{ old('branch', $user->branch) }}" required placeholder="e.g. Westlands"
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('branch') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-gray-400 mt-1">Changing the branch or role regenerates the login username.</p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Username</label>
                        <input type="text" value="{{ $user->username }}" readonly
                               class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-600/50 text-gray-500 dark:text-gray-300 cursor-not-allowed font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Status</label>
                        <select name="is_active"
                                class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('is_active') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">Role</label>
                        @php $currentRoleId = old('role', optional($roles->first(fn($r) => $user->hasRole($r->name)))->id); @endphp
                        <select name="role" required
                                class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $currentRoleId == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
