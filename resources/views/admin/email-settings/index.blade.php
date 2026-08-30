<x-app-layout>
<div class="space-y-6 pt-10 fade-in">
    <div class="rounded-2xl bg-micblue px-6 py-7 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-400">Settings</p>
                <h1 class="mt-0.5 text-xl font-bold text-white">Email Templates</h1>
                <p class="mt-0.5 text-xs text-slate-400">Customize subjects, letterheads and email content sent to users</p>
            </div>
            <button type="button" onclick="document.getElementById('new-template-form').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-white/10 hover:bg-white/20 text-white rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Template
            </button>
        </div>
    </div>

    <div id="new-template-form" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-xs font-bold text-gray-800 dark:text-white mb-4">Create New Template</h3>
        <form method="POST" action="{{ route('admin.email-settings.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Template Key</label>
                <input type="text" name="key" required placeholder="e.g. new_claim_notice"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                <input type="text" name="subject" placeholder="Email subject line"
                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Body (HTML)</label>
                <textarea name="body" rows="4" placeholder="&lt;p&gt;Hello @{{ user_name }},&lt;/p&gt;"
                          class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white font-mono focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">Create Template</button>
            </div>
        </form>
    </div>

    @if($settings->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-10 text-center">
            <p class="text-sm text-gray-400">No email templates configured yet.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6">
            {{-- ─── Template list ─────────────────────────────────────────── --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden self-start">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xs font-bold text-gray-800 dark:text-white">Templates</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $settings->count() }} available</p>
                </div>
                <nav class="p-2 space-y-1" id="template-list">
                    @foreach($settings as $setting)
                        <a href="#tpl-{{ $setting->key }}" data-key="{{ $setting->key }}"
                           class="template-nav group flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-transparent transition cursor-pointer">
                            <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ strtoupper(substr($setting->key, 0, 2)) }}
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</span>
                                <span class="block text-[10px] text-gray-400">{{ $setting->subject }}</span>
                            </span>
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- ─── Editor ────────────────────────────────────────────────── --}}
            <div class="space-y-6">
                @foreach($settings as $setting)
                    @php
                        $vars = match ($setting->key) {
                            'user_credentials' => ['user_name', 'user_email', 'password', 'login_url', 'app_name'],
                            'quotation' => ['customer_name', 'quotation_number', 'company', 'vehicle', 'valuation', 'premium', 'total_premium', 'view_url', 'app_name'],
                            'sticker' => ['customer_name', 'sticker_number', 'company', 'vehicle', 'valuation', 'premium', 'view_url', 'app_name'],
                            'welcome' => ['user_name', 'user_email', 'login_url', 'dashboard_url', 'profile_url', 'app_name'],
                            'pending_email_verification' => ['user_name', 'user_email', 'verify_url', 'app_name'],
                            default => [],
                        };
                    @endphp
                    <div id="tpl-{{ $setting->key }}" class="template-panel bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-500">{{ $setting->key }}</p>
                                <h2 class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ ucwords(str_replace('_', ' ', $setting->key)) }} <span class="ml-2 text-[10px] font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ ucfirst($setting->type) }}</span></h2>
                            </div>
                            <form method="POST" action="{{ route('admin.email-settings.destroy', $setting) }}" onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-300 hover:text-red-500 transition" title="Delete template">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('admin.email-settings.update', $setting) }}" class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Subject</label>
                                    <input type="text" name="subject" value="{{ old('subject', $setting->subject) }}"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Body (HTML)</label>
                                    <div class="editor-toolbar flex flex-wrap items-center gap-0.5 px-2 py-1.5 bg-gray-50 dark:bg-gray-700/50 border border-b-0 border-gray-300 dark:border-gray-600 rounded-t-lg">
                                        <button type="button" data-cmd="bold" title="Bold" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm font-bold">B</button>
                                        <button type="button" data-cmd="italic" title="Italic" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm italic">I</button>
                                        <button type="button" data-cmd="underline" title="Underline" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm underline">U</button>
                                        <span class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></span>
                                        <button type="button" data-cmd="h2" title="Heading" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-bold">H2</button>
                                        <button type="button" data-cmd="h3" title="Sub heading" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-[11px] font-bold">H3</button>
                                        <span class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></span>
                                        <button type="button" data-cmd="link" title="Link" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm">&#128279;</button>
                                        <button type="button" data-cmd="button" title="Button" class="editor-btn px-2 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-[10px] font-semibold">BUTTON</button>
                                        <button type="button" data-cmd="ul" title="Bullet list" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm">&#8226;&nbsp;&#8226;&nbsp;&#8226;</button>
                                        <button type="button" data-cmd="table" title="Table" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm">&#9636;</button>
                                        <span class="w-px h-5 bg-gray-200 dark:bg-gray-600 mx-1"></span>
                                        <button type="button" data-cmd="hr" title="Divider" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-sm">&#9473;</button>
                                        <button type="button" data-cmd="quote" title="Quote" class="editor-btn w-7 h-7 grid place-items-center rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-bold">"</button>
                                        <span class="flex-1"></span>
                                        <span class="text-[10px] text-gray-400 hidden sm:block pr-1">HTML enabled</span>
                                    </div>
                                    <textarea name="body" rows="12"
                                              class="editor-body w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-b-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white font-mono focus:ring-2 focus:ring-blue-500 leading-relaxed">{{ old('body', $setting->body) }}</textarea>
                                    @if(count($vars))
                                        <div class="flex flex-wrap gap-1.5 mt-2">
                                            @foreach($vars as $var)
                                                <button type="button" data-var="{{ $var }}"
                                                        class="var-chip inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                                    {{ $var }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Letterhead</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-medium text-gray-400 mb-1">Underwriter Name</label>
                                            <input type="text" name="letterhead_company" value="{{ old('letterhead_company', $setting->letterhead_company) }}"
                                                   class="lh-company w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-medium text-gray-400 mb-1">Accent Color</label>
                                            <div class="flex items-center gap-2">
                                                <input type="color" name="letterhead_color" value="{{ old('letterhead_color', $setting->letterhead_color ?? '#004f8c') }}"
                                                       class="lh-color h-9 w-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-gray-700">
                                                <span class="lh-color-text text-xs font-mono text-gray-500 dark:text-gray-400">{{ $setting->letterhead_color ?? '#004f8c' }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-medium text-gray-400 mb-1">Website</label>
                                            <input type="text" name="letterhead_website" value="{{ old('letterhead_website', $setting->letterhead_website) }}"
                                                   class="lh-website w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-medium text-gray-400 mb-1">Logo Path <span class="text-gray-300 dark:text-gray-600">(optional — storage path e.g. images/logo.png)</span></label>
                                            <input type="text" name="letterhead_logo" value="{{ old('letterhead_logo', $setting->letterhead_logo) }}"
                                                   class="lh-logo w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white font-mono focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="col-span-2 grid grid-cols-2 gap-3">
                                            <label class="flex items-center gap-2 text-[10px] font-medium text-gray-600 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="letterhead_logo_header" value="1" class="lh-logo-header rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500"
                                                       @checked($setting->letterhead_logo_header)>
                                                Show logo in header
                                            </label>
                                            <label class="flex items-center gap-2 text-[10px] font-medium text-gray-600 dark:text-gray-300 cursor-pointer">
                                                <input type="checkbox" name="letterhead_logo_footer" value="1" class="lh-logo-footer rounded border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500"
                                                       @checked($setting->letterhead_logo_footer)>
                                                Show logo in footer
                                            </label>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-[10px] font-medium text-gray-400 mb-1">Address</label>
                                            <textarea name="letterhead_address" rows="2"
                                                      class="lh-address w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('letterhead_address', $setting->letterhead_address) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-blue-500 hover:bg-blue-400 text-white rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Save Template
                                    </button>
                                </div>
                            </div>

                            {{-- ─── Live preview ──────────────────────────────── --}}
                            <div class="lg:sticky lg:top-20 self-start">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Live Preview</label>
                                    <span class="text-[10px] text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">sample values</span>
                                </div>
                                <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm" style="max-width:520px;margin:0 auto;background:#eef2f7;padding:14px;">
                                    <div class="mx-auto" style="max-width:460px;background:#ffffff;border-radius:10px;overflow:hidden;">
                                        <div class="lh-preview" style="background:#004f8c;padding:18px 22px;">
                                            <div class="lh-preview-img" style="display:none;margin-bottom:6px;">
                                                <img class="lh-preview-logo" src="" alt="" style="max-height:32px;max-width:130px;display:block;">
                                            </div>
                                            <div class="lh-preview-brand" style="color:#fff;font-size:18px;font-weight:800;">MIC Global Risks</div>
                                            <div class="lh-preview-meta" style="color:rgba(255,255,255,.9);font-size:10px;line-height:1.6;margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,.25);">MIC Global Risks (Kenya) Limited, P.O. Box 10000 - 00100 Nairobi, Kenya<br>www.micglobalrisks.com</div>
                                        </div>
                                        <div class="body-preview" style="padding:22px;color:#475569;font-size:12px;line-height:1.7;"></div>
                                        <div class="lh-preview-footer" style="background:#f8fafc;padding:12px 22px;text-align:center;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:10px;">
                                            <div class="lh-preview-footer-img" style="display:none;margin-bottom:4px;">
                                                <img class="lh-preview-logo" src="" alt="" style="max-height:24px;max-width:110px;display:block;margin:0 auto;">
                                            </div>
                                            <div class="lh-preview-footer-text">MIC Global Risks<br>www.micglobalrisks.com</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const sampleValues = {
        user_name: 'John Doe', user_email: 'john@example.com', password: 'P@ssw0rd!',
        login_url: '#', app_name: 'Motor Vehicle Insurance Portal',
        customer_name: 'John Doe', quotation_number: 'Q-2026/0001', company: 'GA Insurance Ltd',
        vehicle: 'Toyota Land Cruiser 2020', valuation: '900,000.00', premium: '58,500.00', total_premium: '62,400.00',
        view_url: '#', sticker_number: 'S-2026/0001', dashboard_url: '#', profile_url: '#',
        verify_url: '#',
    };

    function renderPreview(panel) {
        const input = panel.querySelector('.editor-body');
        const color = panel.querySelector('.lh-color').value;
        const company = panel.querySelector('.lh-company').value || 'MIC Global Risks';
        const website = panel.querySelector('.lh-website').value || 'www.micglobalrisks.com';
        const address = panel.querySelector('.lh-address').value || 'MIC Global Risks (Kenya) Limited, P.O. Box 10000 - 00100 Nairobi, Kenya';
        const logo = panel.querySelector('.lh-logo').value;
        const showHeader = panel.querySelector('.lh-logo-header').checked;
        const showFooter = panel.querySelector('.lh-logo-footer').checked;

        let html = (input.value || '').replace(/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/g, (m, k) => sampleValues[k] ?? '{{' + k + '}}');

        panel.querySelector('.lh-preview').style.background = color;
        const colorText = panel.querySelector('.lh-color-text');
        if (colorText) colorText.textContent = color;
        panel.querySelector('.lh-preview-brand').textContent = company;
        panel.querySelector('.lh-preview-meta').innerHTML = (address + '<br>' + website).replace(/</g, '&lt;');
        panel.querySelector('.lh-preview-footer-text').innerHTML = (company + '<br>' + website).replace(/</g, '&lt;');
        panel.querySelector('.body-preview').innerHTML = html;

        const applyLogo = (img) => {
            img.style.display = 'block';
            img.src = logo ? ('/storage/' + logo) : '/images/MIC-Global-Risks_logo_no_background.png';
            img.onerror = () => { img.style.display = 'none'; };
        };
        const headerWrap = panel.querySelector('.lh-preview-img');
        const footerWrap = panel.querySelector('.lh-preview-footer-img');
        headerWrap.style.display = showHeader ? 'block' : 'none';
        footerWrap.style.display = showFooter ? 'block' : 'none';
        if (showHeader) applyLogo(headerWrap.querySelector('img'));
        if (showFooter) applyLogo(footerWrap.querySelector('img'));
    }

    function wrapSelection(textarea, before, after, placeholder) {
        const start = textarea.selectionStart, end = textarea.selectionEnd;
        const selected = textarea.value.slice(start, end) || placeholder || '';
        textarea.value = textarea.value.slice(0, start) + before + selected + after + textarea.value.slice(end);
        textarea.focus();
        textarea.setSelectionRange(start + before.length, start + before.length + selected.length);
    }

    const editorSnippets = {
        bold: ['<strong>', '</strong>', 'bold text'],
        italic: ['<em>', '</em>', 'italic text'],
        underline: ['<u>', '</u>', 'underline'],
        h2: ['<h2>', '</h2>', 'Heading'],
        h3: ['<h3>', '</h3>', 'Sub heading'],
        link: ['<a href="https://">', '</a>', 'link text'],
        button: ['<p style="text-align:center"><a class="btn" href="https://">', '</a></p>', 'Click Here'],
        ul: ['<ul>\n  <li>', '</li>\n  <li>item</li>\n</ul>', 'Item'],
        table: ['<table>\n  <tr><td>', '</td><td>value</td></tr>\n  <tr><td>', '</td><td>value</td></tr>\n</table>', 'Label'],
        hr: ['<hr>', '', ''],
        quote: ['<blockquote>', '</blockquote>', 'Quoted text'],
    };

    document.querySelectorAll('.template-panel').forEach(panel => {
        panel.querySelectorAll('.lh-color, .lh-company, .lh-website, .lh-address, .lh-logo, .lh-logo-header, .lh-logo-footer, .editor-body').forEach(el => {
            el.addEventListener('input', () => renderPreview(panel));
            el.addEventListener('change', () => renderPreview(panel));
        });
        panel.querySelectorAll('.editor-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const textarea = panel.querySelector('.editor-body');
                const cmd = btn.dataset.cmd;
                if (editorSnippets[cmd]) {
                    const s = editorSnippets[cmd];
                    wrapSelection(textarea, s[0], s[1], s[2]);
                }
                renderPreview(panel);
            });
        });
        panel.querySelectorAll('.var-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const textarea = panel.querySelector('.editor-body');
                const varName = chip.dataset.var;
                const insert = '{{ ' + varName + ' }}';
                const start = textarea.selectionStart, end = textarea.selectionEnd;
                textarea.value = textarea.value.slice(0, start) + insert + textarea.value.slice(end);
                textarea.focus();
                textarea.setSelectionRange(start + insert.length, start + insert.length);
                renderPreview(panel);
            });
        });
        renderPreview(panel);
    });

    const navItems = document.querySelectorAll('.template-nav');
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            navItems.forEach(i => i.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200', 'dark:border-blue-700'));
            item.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'border-blue-200', 'dark:border-blue-700');
        });
    });

    const hash = window.location.hash;
    if (hash && document.querySelector(hash)) {
        document.querySelector(hash).scrollIntoView({ behavior: 'smooth' });
    }
})();
</script>
@endpush
</x-app-layout>
