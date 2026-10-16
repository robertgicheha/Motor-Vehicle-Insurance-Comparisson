<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Access Control</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Create User</h1>
                <p class="mt-0.5 text-xs text-slate-400">Fill in the details to create a new account.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-white/20 text-white/80 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    @if(session('generated_password'))
        <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-6 py-5 shadow-sm fade-in">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">User created successfully!</p>
                        <p class="text-xs text-emerald-500/70 mt-0.5">Save this password. It will not be shown again.</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <code id="generated-password" class="min-w-0 break-all px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-xs font-mono text-gray-800 dark:text-white">{{ session('generated_password') }}</code>
                    <button onclick="copyPassword()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-xs font-semibold text-gray-800 dark:text-white">Account Details</h2>
            <p class="text-[10px] text-gray-400 mt-0.5">All fields marked with * are required. A password is auto-generated below — edit it if needed.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Branch *</label>
                    <input type="text" name="branch" value="{{ old('branch') }}" required placeholder="e.g. Westlands"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <p class="text-[10px] text-gray-400 mt-1">User-role accounts get a branch-based username, e.g. <span class="font-mono">user1_westlands</span>. Admin and Super Admin accounts use a fixed username (<span class="font-mono">admin</span> / <span class="font-mono">superadmin</span>).</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Role *</label>
                    <select name="role" required
                            class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Password *</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="password-field" name="password" required autocomplete="off"
                               class="flex-1 min-w-0 px-3 py-2 text-xs font-mono rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <button type="button" onclick="generatePassword()"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Generate
                        </button>
                        <button type="button" onclick="copyFieldPassword()"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Auto-generated — you can edit it, or click Generate for a new one. The user can change it later from their profile.</p>
                </div>
            </div>

            <div class="mt-5 flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function generateStrongPassword(length = 12) {
    var upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var lower = 'abcdefghijklmnopqrstuvwxyz';
    var numbers = '0123456789';
    var symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    var all = upper + lower + numbers + symbols;
    var pick = function (chars) { return chars[Math.floor(Math.random() * chars.length)]; };

    var chars = [pick(upper), pick(lower), pick(numbers), pick(symbols)];
    for (var i = 4; i < length; i++) {
        chars.push(pick(all));
    }
    for (var j = chars.length - 1; j > 0; j--) {
        var k = Math.floor(Math.random() * (j + 1));
        var tmp = chars[j]; chars[j] = chars[k]; chars[k] = tmp;
    }
    return chars.join('');
}

function generatePassword() {
    document.getElementById('password-field').value = generateStrongPassword();
}

function copyFieldPassword() {
    var field = document.getElementById('password-field');
    if (!field.value) { return; }
    navigator.clipboard.writeText(field.value).then(function() {
        alert('Password copied to clipboard!');
    }).catch(function() {
        field.select();
        document.execCommand('copy');
        alert('Password copied to clipboard!');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var field = document.getElementById('password-field');
    if (field && !field.value) {
        field.value = generateStrongPassword();
    }
});

function copyPassword() {
    var password = document.getElementById('generated-password').textContent;
    navigator.clipboard.writeText(password).then(function() {
        alert('Password copied to clipboard!');
    }).catch(function() {
        var textarea = document.createElement('textarea');
        textarea.value = password;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Password copied to clipboard!');
    });
}
</script>
</x-app-layout>
