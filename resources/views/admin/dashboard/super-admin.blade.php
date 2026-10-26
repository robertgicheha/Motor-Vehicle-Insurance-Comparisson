<x-app-layout>
    <div class="db-root fade-in">
      <div class="db-layout">
      <div class="db-main-content">
      <h2 class="sr-only">LOLC Microfinance Bank Motor Insurance Admin Dashboard</h2>

      <div class="db-top-bar">
        <div class="db-greeting">
          <h1>{{ $greeting }}, {{ auth()->user()->username }}!</h1>
          <p>Super Admin Dashboard &mdash; Motor Insurance Portal</p>
        </div>
        <div class="db-weather" x-data="weatherWidget()" x-init="init()">
          <span style="font-size:36px;line-height:1;display:flex;align-items:center;" x-html="emoji">🌤️</span>
          <div>
            <p class="temp">
              <span x-text="temp">--</span>°C
              <span style="font-size:11px;font-weight:500;color:#7a8baa">Feels <span x-text="feels">--</span>°C</span>
            </p>
            <p class="cond">
              <span x-text="description">Loading...</span> &middot; <span x-text="city">Nairobi</span>
              &middot; <span x-text="humidity">--</span>% RH
              <span x-show="wind" x-text="' · ' + wind + ' m/s wind'"></span>
            </p>
            <p style="font-size:10px;color:#7a8baa;margin:2px 0 0;font-weight:500;">
              <span x-text="localTime"></span>
              <span x-show="sunrise" x-text="' · ☀️ ' + sunrise"></span>
              <span x-show="sunset" x-text="'  🌙 ' + sunset"></span>
              <span x-show="uvi" x-text="' · UV ' + uvi"></span>
            </p>
            <template x-for="alert in alerts" :key="alert.event">
              <p style="font-size:10px;font-weight:700;color:#c0392b;margin:3px 0 0;" x-text="'⚠️ ' + alert.event + ' · ' + alert.start + ' – ' + alert.end"></p>
            </template>
          </div>
        </div>
      </div>

      {{-- ─── PROFILE COMPLETION ALERT ─────────────────────────────────────── --}}
      @if(!auth()->user()->profile_completed)
      <div class="flex items-center justify-between gap-4 px-5 py-4 mb-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 fade-in">
          <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
              </div>
              <div>
                  <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Complete Your Profile</p>
                  <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Please add your phone number and other details to finish setting up your account.</p>
              </div>
          </div>
          <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition shrink-0">
              Complete Profile
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
      </div>
      @endif

      {{-- ─── STICKER NUMBER REMINDER ALERT ──────────────────────────────── --}}
      @if(($stickerNumberReminders ?? collect())->count() > 0)
      <div class="flex items-center justify-between gap-4 px-5 py-4 mb-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 fade-in" id="sticker-number-reminder-banner">
          <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
              </div>
              <div>
                  <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">
                      {{ $stickerNumberReminders->count() }} sticker{{ $stickerNumberReminders->count() > 1 ? 's' : '' }} awaiting the underwriter's sticker number
                  </p>
                  <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Download the underwriter's reply attachment, find the sticker number, and enter it under Update Stickers to mark completion.</p>
              </div>
          </div>
          <a href="{{ route('admin.stickers.update-form') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition shrink-0">
              Update Stickers
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
      </div>
      <script>
      document.addEventListener('DOMContentLoaded', function () {
          if (window._toastManager && !window._stickerReminderToastShown) {
              window._stickerReminderToastShown = true;
              window._toastManager.addToast(
                  {{ Illuminate\Support\Js::from($stickerNumberReminders->count() . ' sticker' . ($stickerNumberReminders->count() > 1 ? 's' : '') . ' awaiting the underwriter\'s sticker number.') }},
                  'warning'
              );
          }
      });
      </script>
      @endif

      @php
        $trendBadge = function ($pct, $label) {
            $up = $pct >= 0;
            $svgPath = $up
                ? '<path d="M3 17l6 -6l4 4l8 -8" /><path d="M14 7l7 0l0 7" />'
                : '<path d="M3 7l6 6l4 -4l8 8" /><path d="M21 10l0 7l-7 0" />';
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 inline">' . $svgPath . '</svg>';
            return '<div class="db-kpi-trend ' . ($up ? 'trend-up' : 'trend-down') . '">' . $icon . ' ' . ($up ? '+' : '') . number_format($pct, 1) . '% ' . $label . '</div>';
        };
      @endphp
      <div class="db-section-label">Key Metrics</div>
      <div class="db-kpi-grid">
        <a href="{{ route('admin.users.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="users" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Total Users</div>
          <div class="db-kpi-value" style="color:#fff">{{ $totalUsers }}</div>
          <div class="text-[10px] text-blue-200/70">{!! $trendBadge($trends['users'], 'this month') !!}</div>
        </a>

        <a href="{{ route('admin.companies.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
        <div class="db-kpi-icon" style="color:#fff"><x-icon name="building" class="w-4 h-4" aria-hidden="true" /></div>
        <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Total Companies</div>
        <div class="db-kpi-value" style="color:#fff">{{ $totalCompanies }}</div>
        <div class="text-[10px] text-blue-200/70">{!! $trendBadge($trends['companies'], 'this week') !!}</div>
      </a>

        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="file-description" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Total Quotations</div>
          <div class="db-kpi-value" style="color:#fff">{{ $totalQuotations }}</div>
          <div class="text-[10px] text-blue-200/70">{!! $trendBadge($trends['quotations'], 'vs last month') !!}</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="coins" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Total Premium (KES)</div>
          <div class="db-kpi-value" style="font-size:20px;color:#fff">{{ number_format($totalPremium) }}</div>
          <div class="text-[10px] text-blue-200/70">{!! $trendBadge($trends['premium'], 'growth') !!}</div>
        </a>
      </div>

      <div class="db-kpi-grid" style="margin-top:14px">
        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="car" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Vehicles Insured</div>
          <div class="db-kpi-value" style="color:#fff">{{ $vehiclesInsured }}</div>
          <div class="db-kpi-sub" style="color:rgba(255,255,255,0.6)">Vehicles with an issued sticker</div>
        </a>
        <a href="{{ route('admin.stickers.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="ticket" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Stickers Issued</div>
          <div class="db-kpi-value" style="color:#fff">{{ $totalStickersIssued }}</div>
          <div class="db-kpi-sub" style="color:rgba(255,255,255,0.6)">Total sticker records</div>
        </a>
        <a href="{{ route('admin.users.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12);cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="mail-forward" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Emails Dispatched</div>
          <div class="db-kpi-value" style="color:#fff">{{ $totalEmailsDispatched }}</div>
          <div class="db-kpi-sub" style="color:rgba(255,255,255,0.6)">To underwriters</div>
        </a>
        <div class="db-kpi" style="--accent:#ED1D25;--icon-bg:rgba(255,255,255,0.15);background-color:#263B8B;border:1px solid rgba(255,255,255,0.12)">
          <div class="db-kpi-icon" style="color:#fff"><x-icon name="wifi" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="color:rgba(255,255,255,0.75)">Online Now</div>
          <div class="db-kpi-value" style="color:#fff">{{ $onlineUsers }}</div>
          <div class="db-kpi-sub" style="color:rgba(255,255,255,0.6)">Active sessions</div>
        </div>
      </div>

      

      <div class="db-section-label">Period Breakdown</div>
      <div class="db-period-grid">
        <a href="{{ route('quotations.index') }}" class="db-period-card" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">Today</div>
          <div class="db-period-value">{{ $todayQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-period-card amber" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">This Week</div>
          <div class="db-period-value">{{ $weekQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-period-card teal" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">This Month</div>
          <div class="db-period-value">{{ $monthQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
      </div>

      {{-- <div class="db-section-label">Quick Quote</div>
      <div style="margin-bottom:8px">@include('dashboard.partials.quick-quote')</div> --}}

      <div class="db-section-label">Quick Actions</div>
      <div class="db-kpi-grid" style="margin-top:0">
        <a href="{{ route('admin.users.create') }}" class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="user-plus" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Add User</div>
          <div class="db-kpi-sub">Create a new user account</div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e1f5ee;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="report" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Generate Reports</div>
          <div class="db-kpi-sub">Export quotation data</div>
        </a>
        <a href="{{ route('admin.companies.create') }}" class="db-kpi" style="--accent:#d4860e;--icon-bg:#fef3c7;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="building-plus" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Add Underwriter</div>
          <div class="db-kpi-sub">Register a new underwriter</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#7c3aed;--icon-bg:#ede9fe;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="file-description" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">View Quotations</div>
          <div class="db-kpi-sub">Browse all quotations</div>
        </a>
        <a href="{{ route('stickers.issue') }}" class="db-kpi" style="--accent:#c0392b;--icon-bg:#fdeaea;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="ticket" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Issue Sticker</div>
          <div class="db-kpi-sub">KYC upload & sticker issuance</div>
        </a>
        <a href="{{ route('admin.email-settings.index') }}" class="db-kpi" style="--accent:#c0392b;--icon-bg:#fdeaea;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="mail-forward" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Email Templates</div>
          <div class="db-kpi-sub">Edit inbound / outbound emails</div>
        </a>
        <a href="{{ route('admin.email-config.index') }}" class="db-kpi" style="--accent:#ED1D25;--icon-bg:#fdeaea;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><x-icon name="settings" class="w-4 h-4" aria-hidden="true" /></div>
          <div class="db-kpi-label" style="margin-bottom:0">Email Settings</div>
          <div class="db-kpi-sub">SMTP &amp; delivery configuration</div>
        </a>
      </div>

      <div class="db-section-label">Analytics</div>
      <div class="db-chart-grid-3col">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Quotations by Company</span></div>
          <canvas id="companyChart" height="200"></canvas>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Monthly Trend</span></div>
          <canvas id="monthlyChart" height="200"></canvas>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Total Distribution of Quotations</span></div>
          <canvas id="distributionChart" height="200"></canvas>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Most Subscribed Premium</span></div>
          <canvas id="premiumChart" height="200"></canvas>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Recent Quotations</span></div>
          <div class="db-table-wrap">
            <table class="db-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>User</th>
                  <th>Company</th>
                  <th>Vehicle</th>
                  <th>Premium (KES)</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentQuotations as $q)
                <tr>
                  <td><a href="{{ route('quotations.show', $q) }}" style="color:#1756c8;font-weight:700">{{ $q->quotation_number }}</a></td>
                  <td>{{ $q->user?->username ?? '—' }}</td>
                  <td>{{ $q->company->name }}</td>
                  <td>{{ $q->car_make }} {{ $q->car_model }}</td>
                  <td>{{ number_format($q->total_premium) }}</td>
                  <td>
                    <span class="db-badge badge-active">{{ ucfirst($q->status) }}</span>
                  </td>
                  <td>{{ $q->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" style="padding:32px;text-align:center;color:#7a8baa;font-size:12px">No quotations yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Companies Breakdown</span></div>
          <div class="db-company-grid">
            @foreach($companies as $company)
            <div class="db-company-card">
              <div class="db-company-name">{{ $company->name }}</div>
              <div class="db-company-label">Total Quotations</div>
              <div class="db-company-count">{{ $company->quotations_count }}</div>
              <div class="db-progress">
                @php $maxCount = $companies->max('quotations_count') ?: 1; @endphp
                <div class="db-progress-fill" style="width:{{ ($company->quotations_count / $maxCount) * 100 }}%"></div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="db-section-label">
        Activity &amp; Alerts
        <span style="float:right;display:inline-flex;gap:6px;">
          <label class="db-auto-refresh" style="display:inline-flex;align-items:center;gap:4px;font-size:10px;color:#7a8baa;cursor:pointer;">
            <input type="checkbox" id="dbActivityAutoRefresh" style="accent-color:#1756c8"> Auto-refresh (30s)
          </label>
          <button type="button" id="dbActivityRefresh" style="border:1px solid #d5deeb;background:#fff;border-radius:6px;padding:2px 10px;font-size:11px;font-weight:600;color:#1756c8;cursor:pointer;">
            <x-icon name="refresh" class="w-3 h-3 inline" /> Refresh
          </button>
        </span>
      </div>
      <div class="db-chart-grid">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>System Activity</span></div>
          <div class="db-activity-list" id="db-system-activity-list">
            @forelse($allActivities as $a)
            @php
              $descLower = strtolower($a->description ?? $a->action);
              $dotColor = str_contains($descLower, 'quotation') ? '#1756c8'
                : (str_contains($descLower, 'sticker') ? '#0f7a65'
                : (str_contains($descLower, 'user') ? '#7c3aed'
                : (str_contains($descLower, 'rate') || str_contains($descLower, 'company') ? '#d4860e'
                : '#c0392b')));
            @endphp
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:{{ $dotColor }}"></div>
              <div>
                <div class="db-activity-text">{{ $a->description ?? $a->action }}</div>
                <div class="db-activity-time">{{ $a->created_at->diffForHumans() }} &middot; {{ $a->user->name ?? 'System' }}</div>
              </div>
            </div>
            @empty
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7a8baa"></div>
              <div>
                <div class="db-activity-text">No activity recorded yet.</div>
                <div class="db-activity-time">System</div>
              </div>
            </div>
            @endforelse
          </div>
          <a href="{{ route('admin.audit.activity') }}" style="display:inline-block;margin-top:10px;font-size:10px;font-weight:600;color:#1756c8;text-decoration:none;">View all activity →</a>
        </div>
        <div class="db-card" style="background-color: #d0d0d0;">
          <div class="db-card-header text-micblue"><span><span class="dot"></span>My Activity</span></div>
          <div class="db-activity-list" id="db-my-activity-list">
            @forelse($myActivities as $a)
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:gray"></div>
              <div>
                <div class="db-activity-text ">{{ $a->description ?? $a->action }}</div>
                <div class="db-activity-time ">{{ $a->created_at->diffForHumans() }}</div>
              </div>
            </div>
            @empty
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7a8baa"></div>
              <div>
                <div class="db-activity-text">No personal activity yet.</div>
                <div class="db-activity-time">Start performing actions to see them here</div>
              </div>
            </div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="db-chart-grid" style="margin-top:14px ">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Quotation Calendar</span></div>
          <div x-data="{
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            selectedDate: null,
            selectedItems: [],
            showModal: false,
            modalDate: '',
            modalQuotes: [],
            quotationDates: {{ json_encode($quotationDates->map(fn($d) => ['date' => $d['date'], 'count' => $d['count']])->values()) }},
            allQuotationData: {{ json_encode($quotationDates->values()) }},
            daysInMonth() { return new Date(this.year, this.month + 1, 0).getDate(); },
            firstDay() { return new Date(this.year, this.month, 1).getDay(); },
            monthName() { return new Date(this.year, this.month).toLocaleString('default', { month: 'long' }); },
            hasQuotations(day) {
              const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
              return this.quotationDates.some(d => d.date === dateStr);
            },
            getDayColors(day) {
              const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
              const dayData = this.allQuotationData.filter(d => d.date === dateStr);
              if (!dayData.length) return [];
              const colors = [];
              dayData.forEach(dd => {
                (dd.items || []).forEach(item => {
                  if (item.company_color && !colors.includes(item.company_color)) colors.push(item.company_color);
                });
              });
              return colors.slice(0, 3);
            },
            getQuotations(day) {
              const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
              return this.allQuotationData.filter(d => d.date === dateStr);
            },
            prevMonth() {
              if (this.month === 0) { this.month = 11; this.year--; }
              else { this.month--; }
              this.selectedDate = null; this.selectedItems = [];
            },
            nextMonth() {
              if (this.month === 11) { this.month = 0; this.year++; }
              else { this.month++; }
              this.selectedDate = null; this.selectedItems = [];
            },
            openModal(day) {
              if (!this.hasQuotations(day)) return;
              this.modalDate = day + ' ' + this.monthName() + ' ' + this.year;
              this.modalQuotes = this.getQuotations(day);
              this.showModal = true;
            },
            closeModal() { this.showModal = false; }
          }">
            <div class="flex items-center justify-between mb-2">
              <button @click="prevMonth()" aria-label="Previous month" class="p-0.5 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-[11px] font-semibold text-gray-800" x-text="monthName() + ' ' + year"></span>
              <button @click="nextMonth()" aria-label="Next month" class="p-0.5 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            <div class="grid grid-cols-7 gap-0.5 text-center mb-0.5">
              <template x-for="d in ['S','M','T','W','T','F','S']" :key="d">
                <div class="text-[9px] font-semibold text-gray-400 py-0.5" x-text="d"></div>
              </template>
            </div>
            <div class="grid grid-cols-7 gap-0.5 text-center">
              <template x-for="blank in firstDay()" :key="'b' + blank">
                <div></div>
              </template>
              <template x-for="day in daysInMonth()" :key="day">
                <div>
                  <div @click="openModal(day)"
                    :class="{
                      'cursor-pointer': hasQuotations(day),
                      'cursor-default': !hasQuotations(day)
                    }"
                    class="relative flex items-center justify-center text-[10px] font-medium text-gray-700 rounded transition"
                    :style="'width:24px;height:24px;margin:0 auto;' + (hasQuotations(day) ? 'background:' + getDayColors(day)[0] + '20;color:' + getDayColors(day)[0] + ';font-weight:700' : '')">
                    <span x-text="day"></span>
                    <template x-if="hasQuotations(day) && getDayColors(day).length > 0">
                      <span class="absolute -bottom-0.5 left-1/2 -translate-x-1/2 flex gap-0.5">
                        <template x-for="(color, ci) in getDayColors(day)" :key="ci">
                          <span :style="'width:3px;height:3px;border-radius:50%;background:' + color"></span>
                        </template>
                        <template x-if="getDayColors(day).length > 3">
                          <span class="text-[6px] leading-none" style="color:#9ca3af">+</span>
                        </template>
                      </span>
                    </template>
                  </div>
                </div>
              </template>
            </div>

            {{-- Modal --}}
            <template x-teleport="body">
              <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(2px)">
                <div @click.away="closeModal()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
                  <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Quotations</p>
                      <h3 class="text-sm font-bold text-gray-800 dark:text-white mt-0.5" x-text="modalDate"></h3>
                    </div>
                    <button @click="closeModal()" aria-label="Close" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                  </div>
                  <div class="px-5 py-3 space-y-2">
                    <template x-for="(dayData, idx) in modalQuotes" :key="idx">
                      <template x-for="(item, iidx) in dayData.items" :key="iidx">
                        <a x-bind:href="'{{ url('quotations') }}/' + item.id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition no-underline">
                          <div :style="'width:4px;height:36px;border-radius:2px;background:' + (item.company_color || '#1756c8')" class="shrink-0"></div>
                          <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                              <span class="text-xs font-bold text-gray-800 dark:text-white" x-text="item.quotation_number"></span>
                              <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded" :style="'background:' + (item.company_color || '#1756c8') + '20;color:' + (item.company_color || '#1756c8')" x-text="item.company"></span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5">
                              <span x-text="item.user_name"></span>
                              <span class="mx-1">&middot;</span>
                              <span x-text="item.car"></span>
                            </p>
                          </div>
                          <div class="text-right shrink-0">
                            <div class="text-xs font-bold text-gray-800 dark:text-white" x-text="'KES ' + Number(item.premium).toLocaleString()"></div>
                          </div>
                        </a>
                      </template>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Notifications &amp; Alerts</span></div>
          <div class="db-activity-list">
            <div class="db-activity-item" style="margin-bottom:6px">
              <div class="db-activity-dot" style="background:#1756c8"></div>
              <div style="font-weight:600;font-size:10px;color:#7a8baa;text-transform:uppercase;letter-spacing:0.6px">Unread Notifications</div>
            </div>
            @forelse($unreadNotifications as $n)
            <a href="{{ route('admin.notifications.show', $n->id) }}" class="db-activity-item" style="text-decoration:none;color:inherit;">
              <div class="db-activity-dot" style="background:#3b82f6"></div>
              <div>
                <div class="db-activity-text">{{ $n->data['message'] ?? $n->data['action'] ?? 'Notification' }}</div>
                <div class="db-activity-time">{{ $n->created_at->diffForHumans() }}</div>
              </div>
            </a>
            @empty
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7a8baa"></div>
              <div>
                <div class="db-activity-text">No unread notifications</div>
                <div class="db-activity-time">You are all caught up</div>
              </div>
            </div>
            @endforelse
            @if($unreadCount > 5)
            <a href="{{ route('admin.notifications.index') }}" class="text-[10px] font-semibold text-blue-600 hover:underline" style="display:block;margin-top:6px;">+{{ $unreadCount - 5 }} more unread</a>
            @endif
          </div>
          <div style="height:10px"></div>
          <div class="db-activity-list">
            <div class="db-activity-item" style="margin-bottom:6px">
              <div class="db-activity-dot" style="background:#c0392b"></div>
              <div style="font-weight:600;font-size:10px;color:#7a8baa;text-transform:uppercase;letter-spacing:0.6px">Recent Audit Alerts</div>
            </div>
            @forelse($recentAlerts as $alert)
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#d4860e"></div>
              <div>
                <div class="db-activity-text">{{ $alert->action }} on {{ $alert->model ?? 'system' }}</div>
                <div class="db-activity-time">{{ $alert->created_at->diffForHumans() }} &middot; {{ $alert->user->name ?? 'System' }}</div>
              </div>
            </div>
            @empty
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7a8baa"></div>
              <div>
                <div class="db-activity-text">No recent alerts</div>
                <div class="db-activity-time">System is operating normally</div>
              </div>
            </div>
            @endforelse
          </div>
          <a href="{{ route('admin.audit.index') }}" style="display:inline-block;margin-top:10px;font-size:10px;font-weight:600;color:#1756c8;text-decoration:none;">View full audit log →</a>
        </div>
      </div>

      <div class="db-section-label">Companies Breakdown</div>
      <div class="db-company-grid">
        @foreach($companies as $company)
        <div class="db-company-card">
          <div class="db-company-name">{{ $company->name }}</div>
          <div class="db-company-label">Total Quotations</div>
          <div class="db-company-count">{{ $company->quotations_count }}</div>
          <div class="db-progress">
            @php $maxCount = $companies->max('quotations_count') ?: 1; @endphp
            <div class="db-progress-fill" style="width:{{ ($company->quotations_count / $maxCount) * 100 }}%"></div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="db-section-label">Recent Quotations</div>
      <div class="db-table-wrap">
        <table class="db-table">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Company</th>
              <th>Vehicle</th>
              <th>Premium (KES)</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentQuotations as $q)
            <tr>
              <td><a href="{{ route('quotations.show', $q) }}" style="color:#1756c8;font-weight:700">{{ $q->quotation_number }}</a></td>
              <td>{{ $q->user?->username ?? '—' }}</td>
              <td>{{ $q->company->name }}</td>
              <td>{{ $q->car_make }} {{ $q->car_model }}</td>
              <td>{{ number_format($q->total_premium) }}</td>
              <td>
                <span class="db-badge badge-active">{{ ucfirst($q->status) }}</span>
              </td>
              <td>{{ $q->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="7" style="padding:32px;text-align:center;color:#7a8baa;font-size:12px">No quotations yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- ── ACTION BAR ── -->
      <div class="db-section-label">Quotation Tools</div>
      <div class="action-bar">
        <span class="action-bar-label">Quotation</span>
        <select id="db-quote-tools-select" style="border:1px solid #d5deeb;border-radius:6px;padding:4px 8px;font-size:11px;color:#1a2540;background:#fff;min-width:220px">
          <option value="">— Select a quotation —</option>
          @foreach($recentQuotations ?? [] as $rq)
          <option value="{{ $rq->id }}" data-download="{{ route('quotations.download.pdf', $rq) }}" data-show="{{ route('quotations.show', $rq) }}">
            {{ $rq->quotation_number }} — {{ $rq->user?->username ?? '—' }}
          </option>
          @endforeach
        </select>
        <a href="#" id="db-quote-tools-download" class="action-btn action-btn-download" onclick="return this.href!=='#'" target="_blank">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download
        </a>
        <a href="#" id="db-quote-tools-print" class="action-btn action-btn-print" onclick="return this.href!=='#'" target="_blank">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Print
        </a>
        <button id="db-quote-tools-preview-btn" @click="$el.closest('.db-root').querySelector('.pdf-preview').classList.toggle('open')" class="action-btn action-btn-preview">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          Preview
        </button>
      </div>

      <!-- ── PDF PREVIEW ── -->
      <div class="pdf-preview">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div style="font-size:11px;font-weight:700;color:#1a2540">Quotation Preview</div>
          <button @click="$el.closest('.pdf-preview').classList.remove('open')" style="background:none;border:none;font-size:16px;color:#7a8baa;cursor:pointer">&times;</button>
        </div>
        <iframe id="db-quote-tools-iframe" srcdoc="<p style='font-family:system-ui;padding:40px;text-align:center;color:#7a8baa'>Select a quotation above to preview it here.</p>"></iframe>
      </div>
      <script>
      document.addEventListener('DOMContentLoaded', function () {
        var select = document.getElementById('db-quote-tools-select');
        var downloadBtn = document.getElementById('db-quote-tools-download');
        var printBtn = document.getElementById('db-quote-tools-print');
        var iframe = document.getElementById('db-quote-tools-iframe');
        if (!select) return;
        select.addEventListener('change', function () {
          var opt = select.options[select.selectedIndex];
          var downloadUrl = opt ? opt.dataset.download : null;
          var showUrl = opt ? opt.dataset.show : null;
          if (downloadUrl) {
            downloadBtn.href = downloadUrl;
            printBtn.href = downloadUrl;
          } else {
            downloadBtn.href = '#';
            printBtn.href = '#';
          }
          if (iframe) {
            iframe.src = showUrl || 'about:blank';
          }
        });
      });
      </script>

      <!-- ── COMPARISON GRID ── -->
      <div class="db-section-label">Compare Insurance Companies</div>
      <div class="comparison-grid">
        @foreach($companies as $company)
        <div class="compare-card">
          <div class="compare-card-logo">
            @php $__cl = $company->logoUrl(); @endphp
            @if($__cl)
              <img src="{{ $__cl }}" alt="{{ $company->name }}">
            @else
              <span style="font-size:20px;font-weight:700;color:#1756c8">{{ substr($company->name, 0, 2) }}</span>
            @endif
          </div>
          <div class="compare-card-name">{{ $company->name }}</div>
          <div class="compare-card-rate">{{ $company->display_rate ?? '—' }}% rate</div>
          <div class="compare-card-premium">KES {{ number_format($company->avg_premium ?? 0) }}</div>
          <a href="{{ route('quotations.index') }}?company={{ urlencode($company->name) }}" class="compare-card-btn">View Quotes</a>
        </div>
        @endforeach
      </div>

      <!-- ── COMPARE TABLE ── -->
      <div class="compare-table-wrap">
        <table>
          <thead>
            <tr>
              <th>Company</th>
              <th>Quotations</th>
              <th>Avg Premium (KES)</th>
              <th>Rate</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($companies as $company)
            <tr>
              <td style="font-weight:700;color:#1a2540">{{ $company->name }}</td>
              <td>{{ $company->quotations_count }}</td>
              <td>KES {{ number_format($company->avg_premium ?? 0) }}</td>
              <td>{{ $company->display_rate ?? '—' }}%</td>
              <td><span class="db-badge badge-active">Active</span></td>
            </tr>
            @endforeach
          </tbody>
          @if($companies->count() > 1)
          <tfoot>
            <tr>
              <td>Total</td>
              <td>{{ $totalQuotations }}</td>
              <td>KES {{ number_format($totalPremium) }}</td>
              <td>&mdash;</td>
              <td>&mdash;</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>

    </div>
    <!-- /db-main-content -->

    {{-- <div class="db-right-sidebar">
      <a href="{{ route('quotations.index') }}" class="cta-card-link">
        <div class="cta-card">
          <div class="cta-card-icon" style="background:#e8f0fc;color:#1756c8"><x-icon name="file-description" class="w-5 h-5" /></div>
          <div class="cta-card-title">All Quotations</div>
          <div class="cta-card-desc">Browse, filter, and manage all quotations</div>
        </div>
      </a>
      <a href="{{ route('admin.reports.index') }}" class="cta-card-link">
        <div class="cta-card">
          <div class="cta-card-icon" style="background:#e1f5ee;color:#0f7a65"><x-icon name="report" class="w-5 h-5" /></div>
          <div class="cta-card-title">Reports</div>
          <div class="cta-card-desc">Export and analyze quotation data</div>
        </div>
      </a>
      <a href="{{ route('admin.audit.index') }}" class="cta-card-link">
        <div class="cta-card">
          <div class="cta-card-icon" style="background:#fef3c7;color:#d4860e"><x-icon name="shield" class="w-5 h-5" /></div>
          <div class="cta-card-title">Audit Log</div>
          <div class="cta-card-desc">Monitor system activity and changes</div>
        </div>
      </a>
      <a href="{{ route('admin.settings.index') }}" class="cta-card-link">
        <div class="cta-card">
          <div class="cta-card-icon" style="background:#fdeaea;color:#c0392b"><x-icon name="settings" class="w-5 h-5" /></div>
          <div class="cta-card-title">Settings</div>
          <div class="cta-card-desc">Configure system preferences</div>
        </div>
      </a>
    </div> --}}
    </div>
    <!-- /db-layout -->

    <div class="db-section-label">Platform Health</div>
      <div class="db-kpi-grid" style="margin-top:0">
        @php
            $healthChecks = $healthChecks ?? collect();
            $healthTypes = ['database' => 'Database', 'site' => 'Website', 'server' => 'Server', 'email_server' => 'Email Server'];
            $healthIcons = ['database' => 'database', 'site' => 'globe', 'server' => 'server', 'email_server' => 'mail'];
        @endphp
        @foreach(['database', 'site', 'server', 'email_server'] as $type)
          @php
            $check = $healthChecks->firstWhere('type', $type);
            $isUp = $check && $check->status === 'up';
            $isDegraded = $check && $check->status === 'degraded';
            $accentColor = $isUp ? '#0f7a65' : ($isDegraded ? '#d4860e' : '#c0392b');
            $iconBg = $isUp ? '#e1f5ee' : ($isDegraded ? '#fef3c7' : '#fdeaea');
            $label = $healthTypes[$type] ?? ucfirst($type);
          @endphp
          <a href="{{ route('admin.health.index') }}" class="db-kpi" style="--accent:{{ $accentColor }};--icon-bg:{{ $iconBg }};cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
            <div class="db-kpi-icon"><x-icon :name="$healthIcons[$type]" class="w-4 h-4" aria-hidden="true" /></div>
            <div class="db-kpi-label">{{ $label }}</div>
            <div class="db-kpi-value" style="font-size:16px">{{ $check ? ucfirst($check->status) : 'Unknown' }}</div>
            <div class="db-kpi-sub" style="font-size:9px">{{ $check ? $check->message : 'Not checked' }}</div>
          </a>
        @endforeach
      </div>

    @push('scripts')
    <script src="{{ asset('js/dashboard-quote.js') }}"></script>
    <script>
    function weatherWidget() {
        return {
            emoji: '{{ $weatherEmoji ?? "🌤️" }}',
            temp: '{{ $weather["temperature"] ?? 25 }}',
            feels: '{{ $weather["feels_like"] ?? 24 }}',
            description: '{{ $weather["description"] ?? "Clear sky" }}',
            city: '{{ $weather["city"] ?? "Nairobi" }}',
            humidity: '{{ $weather["humidity"] ?? 60 }}',
            wind: '{{ $weather["wind_speed"] ?? "" }}',
            localTime: '{{ $weather["local_time"] ?? "" }}',
            sunrise: '{{ $weather["sunrise"] ?? "" }}',
            sunset: '{{ $weather["sunset"] ?? "" }}',
            uvi: '{{ $weather["uvi"] ?? "" }}',
            alerts: {{ \Illuminate\Support\Js::from($weather["alerts"] ?? []) }},
            pollTimer: null,
            coords: null,
            init() {
                // Geolocation prompt disabled: the browser's native permission dialog
                // steals input focus on page load, which made the first click anywhere
                // on the page (including sidebar navigation) appear to do nothing.
                // Server-rendered weather above is shown immediately instead.
                this.fetchWeather();
                this.pollTimer = setInterval(() => this.fetchWeather(), 300000);
            },
            detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => { this.coords = { lat: pos.coords.latitude, lon: pos.coords.longitude }; this.fetchWeather(); },
                        () => { this.coords = null; this.fetchWeather(); },
                        { timeout: 5000, enableHighAccuracy: false }
                    );
                } else {
                    this.fetchWeather();
                }
            },
            fetchWeather() {
                var url = '{{ route("dashboard.weather") }}';
                if (this.coords) {
                    url += '?lat=' + this.coords.lat + '&lon=' + this.coords.lon;
                }
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(w => {
                        this.temp = w.temperature;
                        this.feels = w.feels_like;
                        this.description = w.description;
                        this.city = w.city;
                        this.humidity = w.humidity;
                        this.wind = w.wind_speed || '';
                        this.localTime = w.local_time || '';
                        this.sunrise = w.sunrise || '';
                        this.sunset = w.sunset || '';
                        this.uvi = w.uvi || '';
                        this.alerts = w.alerts || [];
                        var cond = (w.condition || '').toLowerCase();
                        if (cond.includes('clear')) this.emoji = '☀️';
                        else if (cond.includes('cloud')) this.emoji = w.icon && w.icon.startsWith('02') ? '⛅' : '☁️';
                        else if (cond.includes('rain')) this.emoji = '🌧️';
                        else if (cond.includes('drizzle')) this.emoji = '🌦️';
                        else if (cond.includes('thunderstorm')) this.emoji = '⛈️';
                        else if (cond.includes('snow')) this.emoji = '❄️';
                        else if (cond.includes('fog')||cond.includes('mist')||cond.includes('haze')) this.emoji = '🌫️';
                        else if (cond.includes('dust')||cond.includes('sand')||cond.includes('tornado')) this.emoji = '🌪️';
                        else if (cond.includes('squall')) this.emoji = '💨';
                        else this.emoji = '🌤️';
                    })
                    .catch(() => {});
            },
            destroy() { if (this.pollTimer) clearInterval(this.pollTimer); }
        };
    }
    Chart.defaults.font.family = "'Montserrat', sans-serif";
    Chart.defaults.font.size = 11;

    var minimalGrid = { color: 'rgba(0,0,0,0.04)' };
    var minimalTicks = { color: '#7a8baa' };

    var companyChart = new Chart(document.getElementById('companyChart'), {
      type: 'bar',
      data: {
        labels: {{ \Illuminate\Support\Js::from($quotationsByCompany->keys()) }},
        datasets: [{
          label: 'Quotations',
          data: {{ \Illuminate\Support\Js::from($quotationsByCompany->values()) }},
          backgroundColor: '#1756c8',
          borderRadius: 6,
          borderWidth: 0,
          maxBarThickness: 36,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.6,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: minimalGrid, ticks: { ...minimalTicks, precision: 0 } },
          x: { grid: { display: false }, ticks: minimalTicks }
        },
        onClick: function(e, item) {
          if (item.length > 0) {
            var label = this.data.labels[item[0].index];
            window.location.href = '{{ route("quotations.index") }}?company=' + encodeURIComponent(label);
          }
        }
      }
    });

    var monthlyChart = new Chart(document.getElementById('monthlyChart'), {
      type: 'bar',
      data: {
        labels: {{ \Illuminate\Support\Js::from($monthlyQuotations->pluck('month')) }},
        datasets: [{
          label: 'Quotations',
          data: {{ \Illuminate\Support\Js::from($monthlyQuotations->pluck('total')) }},
          backgroundColor: '#1756c8',
          borderRadius: 6,
          borderWidth: 0,
          maxBarThickness: 36,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: minimalGrid, ticks: { ...minimalTicks, precision: 0 } },
          x: { grid: { display: false }, ticks: minimalTicks }
        },
        onClick: function() {
          window.location.href = '{{ route("quotations.index") }}';
        }
      }
    });

    var distributionChart = new Chart(document.getElementById('distributionChart'), {
      type: 'bar',
      data: {
        labels: {{ \Illuminate\Support\Js::from(($statusDistribution ?? collect())->keys()->map(fn($s) => ucfirst($s))) }},
        datasets: [{
          label: 'Quotations',
          data: {{ \Illuminate\Support\Js::from(($statusDistribution ?? collect())->values()) }},
          backgroundColor: ['#1756c8', '#0f7a65', '#d4860e', '#7c3aed'],
          borderRadius: 6,
          borderWidth: 0,
          maxBarThickness: 48,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: minimalGrid, ticks: { ...minimalTicks, precision: 0 } },
          x: { grid: { display: false }, ticks: minimalTicks }
        }
      }
    });

    var premiumChart = new Chart(document.getElementById('premiumChart'), {
      type: 'bar',
      data: {
        labels: {{ \Illuminate\Support\Js::from($premiumTrend->pluck('month')) }},
        datasets: [{
          label: 'Avg Premium (KES)',
          data: {{ \Illuminate\Support\Js::from($premiumTrend->pluck('avg_premium')) }},
          backgroundColor: '#7c3aed',
          borderRadius: 6,
          borderWidth: 0,
          maxBarThickness: 36,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            grid: minimalGrid,
            ticks: { color: '#7a8baa', callback: v => 'KES ' + Number(v).toLocaleString() }
          },
          x: { grid: { display: false }, ticks: minimalTicks }
        }
      }
    });

    function renderActivityList(container, activities) {
      if (!container) return;
      if (!activities.length) {
        container.innerHTML = '<div class="db-activity-item"><div class="db-activity-dot" style="background:#7a8baa"></div><div><div class="db-activity-text">No activity recorded yet.</div><div class="db-activity-time">System</div></div></div>';
        return;
      }
      container.innerHTML = activities.map(function (a) {
        return '<div class="db-activity-item">' +
          '<div class="db-activity-dot" style="background:' + a.dot_color + '"></div>' +
          '<div>' +
            '<div class="db-activity-text"></div>' +
            '<div class="db-activity-time"></div>' +
          '</div>' +
        '</div>';
      }).join('');
      var items = container.querySelectorAll('.db-activity-item');
      activities.forEach(function (a, i) {
        items[i].querySelector('.db-activity-text').textContent = a.text;
        items[i].querySelector('.db-activity-time').textContent = a.time + ' · ' + a.user;
      });
    }

    function fetchActivityFeed(scope, containerId) {
      return fetch('{{ route("dashboard.activity-feed") }}?scope=' + scope, { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.json(); })
        .then(function (res) { renderActivityList(document.getElementById(containerId), res.activities || []); });
    }

    document.addEventListener('DOMContentLoaded', function () {
      var btn = document.getElementById('dbActivityRefresh');
      var auto = document.getElementById('dbActivityAutoRefresh');
      if (!btn) return;
      var saved = localStorage.getItem('db_activity_auto_refresh');
      if (auto && saved === '1') auto.checked = true;
      var timer = null;
      function doRefresh() {
        return Promise.all([
          fetchActivityFeed('system', 'db-system-activity-list'),
          fetchActivityFeed('mine', 'db-my-activity-list'),
        ]);
      }
      function schedule() {
        if (timer) clearInterval(timer);
        if (auto && auto.checked) timer = setInterval(doRefresh, 30000);
      }
      if (auto) {
        auto.addEventListener('change', function () {
          localStorage.setItem('db_activity_auto_refresh', auto.checked ? '1' : '0');
          schedule();
        });
      }
      btn.addEventListener('click', function () {
        btn.disabled = true;
        var original = btn.innerHTML;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;display:inline-block;vertical-align:middle"><path d="M12 6l0 -3" /><path d="M16.25 7.75l2.15 -2.15" /><path d="M18 12l3 0" /><path d="M16.25 16.25l2.15 2.15" /><path d="M12 18l0 3" /><path d="M7.75 16.25l-2.15 2.15" /><path d="M6 12l-3 0" /><path d="M7.75 7.75l-2.15 -2.15" /></svg> Refreshing...';
        doRefresh().finally(function () {
          btn.disabled = false;
          btn.innerHTML = original;
        });
      });
      schedule();
    });
    </script>
    @endpush
</x-app-layout>