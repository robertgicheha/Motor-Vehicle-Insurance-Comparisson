<x-app-layout>

{{-- ══════════════════════════════════════════════════════════
     PAGE HERO
══════════════════════════════════════════════════════════ --}}
<div class="qf-hero">
    <div class="qf-hero__inner">
        <div>
            <span class="qf-hero__eyebrow">Motor Insurance</span>
            <h1 class="qf-hero__title">
                @if(request('user_id') && isset($targetUser))
                    New Quotation — <span class="qf-hero__accent">{{ $targetUser->name }}</span>
                @else
                    Get Your <span class="qf-hero__accent">Motor Insurance Quote</span>
                @endif
            </h1>
            <p class="qf-hero__sub">Complete the steps below to generate a personalised insurance quotation.</p>
        </div>
        @if(request('user_id') && isset($targetUser))
        <div class="qf-hero__badge">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Applying as admin for <strong>{{ $targetUser->name }}</strong>
        </div>
        @endif
    </div>
    <div class="qf-hero__grid" aria-hidden="true"></div>
</div>

{{-- ══════════════════════════════════════════════════════════
     FORM CONTAINER
══════════════════════════════════════════════════════════ --}}
<div class="qf-wrap fade-in">

    {{-- Hidden field for admin applying for a user --}}
    @if(request('user_id'))
    <input type="hidden" id="f-user-id" value="{{ request('user_id') }}">
    @endif

    {{-- Step Indicators --}}
    <div class="qf-steps">
        <div class="qf-step active" id="step-tab-1" onclick="goStep(1)">
            <div class="qf-step__num">1</div>
            <div class="qf-step__label">
                <span class="qf-step__title">Personal Details</span>
                <span class="qf-step__sub">Contact information</span>
            </div>
        </div>
        <div class="qf-step__connector"></div>
        <div class="qf-step" id="step-tab-2" onclick="goStep(2)">
            <div class="qf-step__num">2</div>
            <div class="qf-step__label">
                <span class="qf-step__title">Vehicle Details</span>
                <span class="qf-step__sub">Car information</span>
            </div>
        </div>
        <div class="qf-step__connector"></div>
        <div class="qf-step" id="step-tab-3" onclick="goStep(3)">
            <div class="qf-step__num">3</div>
            <div class="qf-step__label">
                <span class="qf-step__title">Quote Generation</span>
                <span class="qf-step__sub">Premium estimates</span>
            </div>
        </div>
    </div>

    {{-- ── STEP 1: Personal Details ── --}}
    <div class="qf-panel active" id="panel-1">
        <div class="qf-section-head">
            <h2 class="qf-section-title">Personal Details</h2>
            <p class="qf-section-sub">Please fill in the contact details of the insured party.</p>
        </div>
        <div class="qf-grid qf-grid--3">
            <div class="qf-group">
                <label class="qf-label">Salutation <span class="qf-req">*</span></label>
                <select id="f-salutation" class="qf-select">
                    <option value="">Select...</option>
                    <option>Mr.</option><option>Mrs.</option><option>Miss</option><option>Dr.</option><option>Prof.</option>
                </select>
            </div>
            <div class="qf-group">
                <label class="qf-label">First Name <span class="qf-req">*</span></label>
                <input type="text" id="f-first" class="qf-input" placeholder="e.g. John">
            </div>
            <div class="qf-group">
                <label class="qf-label">Middle Name</label>
                <input type="text" id="f-middle" class="qf-input" placeholder="Optional">
            </div>
            <div class="qf-group">
                <label class="qf-label">Last Name <span class="qf-req">*</span></label>
                <input type="text" id="f-last" class="qf-input" placeholder="e.g. Doe">
            </div>
            <div class="qf-group">
                <label class="qf-label">Phone Number <span class="qf-req">*</span></label>
                <input type="tel" id="f-phone" class="qf-input" placeholder="07 XX XXX XXX">
            </div>
            <div class="qf-group">
                <label class="qf-label">Email Address <span class="qf-req">*</span></label>
                <input type="email" id="f-email" class="qf-input" placeholder="email@example.com">
            </div>
        </div>
        <div class="qf-btn-row">
            <a href="{{ route('quotations.index') }}" class="qfbtn qfbtn--ghost">← Cancel</a>
            <button class="qfbtn qfbtn--primary" onclick="goStep(2)">Continue to Vehicle Details →</button>
        </div>
    </div>

    {{-- ── STEP 2: Vehicle Details ── --}}
    <div class="qf-panel" id="panel-2">
        <div class="qf-section-head">
            <h2 class="qf-section-title">Vehicle Details</h2>
            <p class="qf-section-sub">Provide accurate vehicle information for a precise premium calculation.</p>
        </div>
        <div class="qf-grid">
            <div class="qf-group">
                <label class="qf-label">Registration Number <span class="qf-req">*</span></label>
                <input type="text" id="f-reg" class="qf-input" placeholder="e.g. KDH 724V">
            </div>
            <div class="qf-group">
                <label class="qf-label">Chassis / Frame Number</label>
                <input type="text" id="f-chassis" class="qf-input" placeholder="e.g. WVWZZZ1KZ9W123456">
            </div>
            <div class="qf-group">
                <label class="qf-label">Make of Vehicle <span class="qf-req">*</span></label>
                <select id="f-make" class="qf-select" onchange="toggleOtherMake(this)">
                    <option value="">Select make...</option>
                    <option>Toyota</option><option>Honda</option><option>Volkswagen</option>
                    <option>Subaru</option><option>Nissan</option><option>Mitsubishi</option>
                    <option>Mazda</option><option>Mercedes-Benz</option><option>BMW</option>
                    <option>Ford</option><option>Other</option>
                </select>
                <input type="text" id="f-make-other" class="qf-input" placeholder="Type your vehicle make..." style="display:none;margin-top:8px">
            </div>
            <div class="qf-group">
                <label class="qf-label">Model Name <span class="qf-req">*</span></label>
                <input type="text" id="f-model" class="qf-input" placeholder="e.g. Vezel, Mark X, Premio">
            </div>
            <div class="qf-group">
                <label class="qf-label">Body Type</label>
                <select id="f-body" class="qf-select">
                    <option value="">Select body type...</option>
                    <option>Saloon</option><option>Station Wagon (S. Wagon)</option><option>SUV/4x4</option>
                    <option>Hatchback</option><option>Pick-Up</option><option>Van/Minivan</option>
                    <option>Convertible</option><option>Coupe</option>
                </select>
            </div>
            <div class="qf-group">
                <label class="qf-label">Year of Manufacture <span class="qf-req">*</span></label>
                <select id="f-year" class="qf-select"></select>
            </div>
            <div class="qf-group">
                <label class="qf-label">Color</label>
                <select id="f-color" class="qf-select" onchange="toggleOtherColor(this)">
                    <option value="">Select color...</option>
                    <option>White</option><option>Black</option><option>Silver/Grey</option>
                    <option>Blue</option><option>Red</option><option>Brown/Maroon</option>
                    <option>Gold/Champagne</option><option>Green</option><option>Orange</option>
                    <option>Purple</option><option>Other</option>
                </select>
                <input type="text" id="f-color-other" class="qf-input" placeholder="Type your vehicle color..." style="display:none;margin-top:8px">
            </div>
            <div class="qf-group">
                <label class="qf-label">Value of Vehicle (KES) <span class="qf-req">*</span></label>
                <div class="qf-input-prefix">
                    <span class="qf-prefix">KES</span>
                    <input type="number" id="f-value" class="qf-input qf-input--prefixed" placeholder="0" oninput="updatePremiumPreview()">
                </div>
            </div>
        </div>
        <div class="qf-btn-row">
            <button class="qfbtn qfbtn--ghost" onclick="goStep(1)">← Back</button>
            <button class="qfbtn qfbtn--primary" onclick="goStep(3)">Continue to Quote Generation →</button>
        </div>
    </div>

    {{-- ── STEP 3: Quote Generation ── --}}
    <div class="qf-panel" id="panel-3">
        <div class="qf-section-head">
            <h2 class="qf-section-title">Premium Estimate</h2>
            <p class="qf-section-sub">Select an insurer card to open its full quotation, or compare all three side by side.</p>
        </div>

        {{-- Empty state --}}
        <div id="premium-preview-empty" class="qf-empty-box">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px;display:block;color:#9CA3AF"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Go back to Step 2 and enter the <strong>vehicle value</strong> to see premium estimates.
        </div>

        {{-- Quote cards --}}
        <div id="premium-preview" style="display:none">
            <div class="qf-quote-grid">

                {{-- GA Insurance --}}
                <div class="qf-card" onclick="openQuotePage('GA')">
                    <div class="qf-card__logo-wrap">
                        <img src="{{ asset('images/GA Insurance.png') }}" alt="GA Insurance" class="qf-card__logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="qf-card__logo-fallback" style="display:none;color:#003087">GA</div>
                    </div>
                    <div class="qf-card__name" style="color:#003087">GA Insurance</div>
                    <div class="qf-card__rate">@ 3.5%</div>
                    <div class="qf-card__premium" id="prev-ga" style="color:#003087">—</div>
                    <div class="qf-card__cta" style="background:#003087">View Quote →</div>
                </div>

                {{-- Britam --}}
                <div class="qf-card" onclick="openQuotePage('BRITAM')">
                    <div class="qf-card__logo-wrap">
                        <img src="{{ asset('images/britam_logo.png') }}" alt="Britam" class="qf-card__logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="qf-card__logo-fallback" style="display:none;color:#E60028">BR</div>
                    </div>
                    <div class="qf-card__name" style="color:#E60028">Britam</div>
                    <div class="qf-card__rate">@ 4.1%</div>
                    <div class="qf-card__premium" id="prev-britam" style="color:#E60028">—</div>
                    <div class="qf-card__cta" style="background:#E60028">View Quote →</div>
                </div>

                {{-- Sanlam Allianz --}}
                <div class="qf-card" onclick="openQuotePage('SANLAM')">
                    <div class="qf-card__logo-wrap">
                        <img src="{{ asset('images/Sanlam-Allianz.png') }}" alt="Sanlam Allianz" class="qf-card__logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="qf-card__logo-fallback" style="display:none;color:#0066B3">SA</div>
                    </div>
                    <div class="qf-card__name" style="color:#0066B3">Sanlam Allianz</div>
                    <div class="qf-card__rate">@ 4.5%</div>
                    <div class="qf-card__premium" id="prev-sanlam" style="color:#0066B3">—</div>
                    <div class="qf-card__cta" style="background:#0066B3">View Quote →</div>
                </div>

                {{-- Compare All --}}
                <div class="qf-card qf-card--compare" onclick="openQuotePage('ALL')">
                    <div class="qf-card__logo-wrap">
                        <img src="{{ asset('images/benchmarking.png') }}" alt="Compare All" class="qf-card__logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="qf-card__logo-fallback" style="display:none;color:#CC0000">📊</div>
                    </div>
                    <div class="qf-card__name" style="color:#CC0000">Compare All</div>
                    <div class="qf-card__rate">All insurers</div>
                    <div class="qf-card__premium" style="color:#CC0000">3 Quotes</div>
                    <div class="qf-card__cta" style="background:#CC0000">Compare →</div>
                </div>

            </div>

            {{-- Summary row --}}
            <div class="qf-summary" id="qf-summary" style="display:none">
                <div class="qf-summary__item">
                    <span class="qf-summary__label">Vehicle Value</span>
                    <span class="qf-summary__val" id="sum-value">—</span>
                </div>
                <div class="qf-summary__divider"></div>
                <div class="qf-summary__item">
                    <span class="qf-summary__label">Lowest Premium</span>
                    <span class="qf-summary__val qf-summary__val--green" id="sum-lowest">—</span>
                </div>
                <div class="qf-summary__divider"></div>
                <div class="qf-summary__item">
                    <span class="qf-summary__label">Highest Premium</span>
                    <span class="qf-summary__val" id="sum-highest">—</span>
                </div>
            </div>
        </div>

        <div class="qf-btn-row">
            <button class="qfbtn qfbtn--ghost" onclick="goStep(2)">← Back to Vehicle Details</button>
        </div>
    </div>

</div><!-- /qf-wrap -->

{{-- ══════════════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════════════ --}}
<style>
:root {
    --qf-navy:   #0A1F44;
    --qf-blue:   #0066B3;
    --qf-blue-lt:#E8F0FA;
    --qf-red:    #CC0000;
    --qf-border: #E5E9F2;
    --qf-text:   #111827;
    --qf-muted:  #6B7280;
    --qf-radius: 10px;
}

/* ── Hero ── */
.qf-hero {
    position: relative;
    background: linear-gradient(135deg, var(--qf-navy) 0%, #1a3a6e 60%, #0066B3 100%);
    padding: 44px 32px 40px;
    overflow: hidden;
}
.qf-hero__inner {
    max-width: 860px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    position: relative;
    z-index: 1;
}
.qf-hero__grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.qf-hero__eyebrow {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgba(255,255,255,.55);
    margin-bottom: 10px;
    border-left: 3px solid var(--qf-red);
    padding-left: 10px;
}
.qf-hero__title {
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    margin: 0 0 10px;
    letter-spacing: -.02em;
}
.qf-hero__accent { color: #6EB4F7; }
.qf-hero__sub { font-size: 13px; color: rgba(255,255,255,.65); margin: 0; line-height: 1.6; }
.qf-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    padding: 10px 16px;
    border-radius: 8px;
    color: #fff;
    font-size: 12px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* ── Form Wrap ── */
.qf-wrap {
    max-width: 860px;
    margin: 28px auto;
    padding: 0 20px;
}

/* ── Steps ── */
.qf-steps {
    display: flex;
    align-items: center;
    margin-bottom: 28px;
}
.qf-step {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 14px 18px;
    border-radius: var(--qf-radius);
    border: 1.5px solid var(--qf-border);
    background: #fff;
    transition: all .15s;
    flex: 1;
}
.qf-step:hover { border-color: var(--qf-blue); }
.qf-step.active { border-color: var(--qf-blue); background: var(--qf-blue-lt); }
.qf-step.done { border-color: #16A34A; background: #039d31; }
.qf-step__num {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--qf-border);
    color: var(--qf-muted);
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all .15s;
}
.qf-step.active .qf-step__num { background: var(--qf-blue); color: #fff; }
.qf-step.done .qf-step__num { background: #16A34A; color: #fff; }
.qf-step__label { display: flex; flex-direction: column; }
.qf-step__title { font-size: 13px; font-weight: 700; color: var(--qf-text); }
.qf-step__sub { font-size: 11px; color: var(--qf-muted); }
.qf-step__connector {
    flex: 0 0 24px;
    height: 2px;
    background: var(--qf-border);
    margin: 0 4px;
}

/* ── Panel ── */
.qf-panel { display: none; }
.qf-panel.active { display: block; }

.qf-section-head { margin-bottom: 22px; }
.qf-section-title { font-size: 17px; font-weight: 700; color: var(--qf-navy); margin: 0 0 4px; }
.qf-section-sub { font-size: 13px; color: var(--qf-muted); margin: 0; }

/* ── Form Grid ── */
.qf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.qf-grid--3 { grid-template-columns: repeat(3, 1fr); }
.qf-group { display: flex; flex-direction: column; }
.qf-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }
.qf-req { color: var(--qf-red); }
.qf-input, .qf-select {
    padding: 9px 13px;
    border: 1.5px solid var(--qf-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--qf-text);
    outline: none;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
}
.qf-input:focus, .qf-select:focus {
    border-color: var(--qf-blue);
    box-shadow: 0 0 0 3px rgba(0,102,179,.1);
}
.qf-input-prefix { position: relative; display: flex; }
.qf-prefix {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    font-weight: 700;
    color: var(--qf-muted);
    pointer-events: none;
}
.qf-input--prefixed { padding-left: 42px; width: 100%; }

/* ── Buttons ── */
.qf-btn-row { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; }
.qfbtn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 22px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .15s;
}
.qfbtn--primary { background: var(--qf-blue); color: #fff; }
.qfbtn--primary:hover { background: #0052a3; }
.qfbtn--ghost {
    background: #fff;
    color: var(--qf-muted);
    border: 1.5px solid var(--qf-border);
}
.qfbtn--ghost:hover { border-color: var(--qf-blue); color: var(--qf-blue); }

/* ── Empty state ── */
.qf-empty-box {
    text-align: center;
    padding: 36px 24px;
    border: 1.5px dashed var(--qf-border);
    border-radius: 12px;
    color: var(--qf-muted);
    font-size: 13px;
    line-height: 1.7;
    margin-bottom: 20px;
}

/* ── Quote Cards ── */
.qf-quote-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
.qf-card {
    background: #fff;
    border: 1.5px solid var(--qf-border);
    border-radius: 12px;
    padding: 20px 16px;
    text-align: center;
    cursor: pointer;
    transition: box-shadow .2s, transform .15s, border-color .15s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.qf-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
.qf-card--compare { border-color: rgba(204,0,0,.3); }
.qf-card__logo-wrap {
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 4px;
}
.qf-card__logo { max-height: 34px; max-width: 100px; object-fit: contain; }
.qf-card__logo-fallback {
    font-size: 13px; font-weight: 800; display: none; align-items: center; justify-content: center;
}
.qf-card__name { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
.qf-card__rate { font-size: 11px; color: var(--qf-muted); }
.qf-card__premium { font-size: 1.15rem; font-weight: 800; line-height: 1.2; }
.qf-card__cta {
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    border-radius: 6px;
    padding: 6px 12px;
    margin-top: 6px;
    width: 100%;
    box-sizing: border-box;
}

/* ── Summary ── */
.qf-summary {
    display: flex;
    align-items: center;
    gap: 0;
    background: var(--qf-blue-lt);
    border: 1.5px solid #C5D8EF;
    border-radius: 10px;
    padding: 14px 24px;
    margin-bottom: 8px;
}
.qf-summary__item { flex: 1; text-align: center; }
.qf-summary__label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .07em; color: var(--qf-muted); font-weight: 600; margin-bottom: 3px; }
.qf-summary__val { font-size: 15px; font-weight: 700; color: var(--qf-navy); }
.qf-summary__val--green { color: #16A34A; }
.qf-summary__divider { width: 1px; height: 36px; background: #C5D8EF; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .qf-hero__inner { flex-direction: column; align-items: flex-start; }
    .qf-hero__badge { margin-top: 12px; }
    .qf-steps { flex-direction: column; gap: 6px; }
    .qf-step__connector { display: none; }
    .qf-step { width: 100%; }
    .qf-grid, .qf-grid--3 { grid-template-columns: 1fr; }
    .qf-quote-grid { grid-template-columns: 1fr 1fr; }
    .qf-btn-row { justify-content: stretch; flex-direction: column-reverse; }
    .qfbtn { justify-content: center; }
}
@media (max-width: 460px) {
    .qf-quote-grid { grid-template-columns: 1fr; }
}
</style>

{{-- ══════════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════ --}}
<script>
// ── Populate year dropdown ──
(function () {
    const sel = document.getElementById('f-year');
    const max = new Date().getFullYear();
    for (let y = max; y >= 1990; y--) {
        const opt = document.createElement('option');
        opt.value = y; opt.textContent = y;
        sel.appendChild(opt);
    }
})();

// ── Step navigation ──
function goStep(n) {
    [1,2,3].forEach(i => {
        document.getElementById('step-tab-' + i).classList.remove('active','done');
        document.getElementById('panel-' + i).classList.remove('active');
    });
    for (let i = 1; i < n; i++) {
        document.getElementById('step-tab-' + i).classList.add('done');
    }
    document.getElementById('step-tab-' + n).classList.add('active');
    document.getElementById('panel-' + n).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Other make/color toggles ──
function toggleOtherMake(sel) {
    document.getElementById('f-make-other').style.display = sel.value === 'Other' ? '' : 'none';
}
function toggleOtherColor(sel) {
    document.getElementById('f-color-other').style.display = sel.value === 'Other' ? '' : 'none';
}

// ── Premium preview ──
function fmt(n) { return 'KES ' + n.toLocaleString('en-KE', {minimumFractionDigits:0, maximumFractionDigits:0}); }

function updatePremiumPreview() {
    const val = parseFloat(document.getElementById('f-value').value) || 0;
    const empty = document.getElementById('premium-preview-empty');
    const preview = document.getElementById('premium-preview');
    const summary = document.getElementById('qf-summary');

    if (val < 1) {
        empty.style.display = '';
        preview.style.display = 'none';
        return;
    }
    empty.style.display = 'none';
    preview.style.display = '';

    const rates = { ga: 0.035, britam: 0.041, sanlam: 0.045 };
    const tax = 0.005; // 0.5% levy

    const ga     = val * (rates.ga + tax);
    const britam = val * (rates.britam + tax);
    const sanlam = val * (rates.sanlam + tax);

    document.getElementById('prev-ga').textContent     = fmt(ga);
    document.getElementById('prev-britam').textContent = fmt(britam);
    document.getElementById('prev-sanlam').textContent = fmt(sanlam);

    document.getElementById('sum-value').textContent   = fmt(val);
    document.getElementById('sum-lowest').textContent  = fmt(ga);
    document.getElementById('sum-highest').textContent = fmt(sanlam);
    summary.style.display = '';
}

// ── Open quote page (stub — wire to your actual routes) ──
function openQuotePage(insurer) {
    const userId = document.getElementById('f-user-id')?.value || null;
    const data = {
        insurer,
        salutation : document.getElementById('f-salutation').value,
        first      : document.getElementById('f-first').value,
        middle     : document.getElementById('f-middle').value,
        last       : document.getElementById('f-last').value,
        phone      : document.getElementById('f-phone').value,
        email      : document.getElementById('f-email').value,
        reg        : document.getElementById('f-reg').value,
        chassis    : document.getElementById('f-chassis').value,
        make       : document.getElementById('f-make').value === 'Other'
                        ? document.getElementById('f-make-other').value
                        : document.getElementById('f-make').value,
        model      : document.getElementById('f-model').value,
        body       : document.getElementById('f-body').value,
        year       : document.getElementById('f-year').value,
        color      : document.getElementById('f-color').value === 'Other'
                        ? document.getElementById('f-color-other').value
                        : document.getElementById('f-color').value,
        value      : document.getElementById('f-value').value,
        userId,
    };
    // Store in sessionStorage for the quote page to consume
    sessionStorage.setItem('micQuoteData', JSON.stringify(data));
    // Navigate — adjust route as needed
    window.location.href = "{{ route('quotations.preview') }}?insurer=" + insurer + (userId ? '&user_id=' + userId : '');
}
</script>

</x-app-layout>