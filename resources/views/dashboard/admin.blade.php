<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <div class="db-root fade-in">
      <div class="db-layout">
      <div class="db-main-content">
      <h2 class="sr-only">MIC Admin Dashboard</h2>

      <div class="db-top-bar">
        <div class="db-greeting">
          <h1>{{ $greeting ?? 'Welcome' }}, {{ auth()->user()->first_name ?: auth()->user()->name }}!</h1>
          <p>Admin Dashboard &mdash; Motor Insurance Portal</p>
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

      <div class="db-section-label">Key Metrics</div>
      <div class="db-kpi-grid">
        <a href="{{ route('admin.users.index') }}" class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc;cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon"><i class="ti ti-users" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Users</div>
          <div class="db-kpi-value">{{ $totalUsers ?? 0 }}</div>
          <div class="db-kpi-trend trend-neutral"><i class="ti ti-users" style="font-size:12px"></i> {{ $activeUsers ?? 0 }} active</div>
        </a>

        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e8f5f0;cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon"><i class="ti ti-file-description" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Quotations</div>
          <div class="db-kpi-value">{{ $totalQuotations ?? 0 }}</div>
          <div class="db-kpi-trend trend-up"><i class="ti ti-trending-up" style="font-size:12px"></i> +{{ $todayQuotations ?? 0 }} today</div>
        </a>

        <a href="{{ route('admin.claims.index') }}" class="db-kpi" style="--accent:#d4860e;--icon-bg:#fef5e7;cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon"><i class="ti ti-article" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Claims</div>
          <div class="db-kpi-value">{{ $totalClaims ?? 0 }}</div>
          <div class="db-kpi-sub">All submitted claims</div>
        </a>

        <a href="{{ route('admin.stickers.index') }}" class="db-kpi" style="--accent:#c0392b;--icon-bg:#fce8e8;cursor:pointer;text-decoration:none;">
          <div class="db-kpi-icon"><i class="ti ti-ticket" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Stickers / Certificates</div>
          <div class="db-kpi-value">{{ $totalStickers ?? 0 }}</div>
          <div class="db-kpi-sub">Across all policies</div>
        </a>
      </div>

      <div class="db-section-label">Period Breakdown</div>
      <div class="db-period-grid">
        <a href="{{ route('quotations.index') }}" class="db-period-card" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">Today</div>
          <div class="db-period-value">{{ $todayQuotations ?? 0 }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-period-card amber" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">This Week</div>
          <div class="db-period-value">{{ $weekQuotations ?? 0 }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-period-card teal" style="cursor:pointer;text-decoration:none;">
          <div class="db-period-label">This Month</div>
          <div class="db-period-value">{{ $monthQuotations ?? 0 }}</div>
          <div class="db-period-desc">quotations generated</div>
        </a>
      </div>

      <div class="db-section-label">Quick Quote</div>
      <div style="margin-bottom:8px">@include('dashboard.partials.quick-quote')</div>

      <div class="db-section-label">Quick Actions</div>
      <div class="db-kpi-grid" style="margin-top:0">
        <a href="{{ route('admin.users.create') }}" class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-user-plus" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Create User</div>
          <div class="db-kpi-sub">Email credentials to new user</div>
        </a>
        <a href="{{ route('claims.create') }}" class="db-kpi" style="--accent:#d4860e;--icon-bg:#fef5e7;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-file-plus" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Log Claim</div>
          <div class="db-kpi-sub">For you, a user or customer</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e8f5f0;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-file-description" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">View Quotations</div>
          <div class="db-kpi-sub">Browse all quotations</div>
        </a>
        <a href="{{ route('stickers.issue') }}" class="db-kpi" style="--accent:#c0392b;--icon-bg:#fdeaea;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-ticket" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Issue Certificate</div>
          <div class="db-kpi-sub">KYC upload & sticker issuance</div>
        </a>
        <a href="{{ route('admin.email-settings.index') }}" class="db-kpi" style="--accent:#7c3aed;--icon-bg:#ede9fe;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-mail-forward" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Email Templates</div>
          <div class="db-kpi-sub">Edit inbound / outbound emails</div>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="db-kpi" style="--accent:#c0392b;--icon-bg:#fdeaea;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-settings" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Mail Settings</div>
          <div class="db-kpi-sub">SMTP &amp; delivery configuration</div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="db-kpi" style="--accent:#1E40AF;--icon-bg:#e0edff;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-report" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Generate Reports</div>
          <div class="db-kpi-sub">PDF / Excel / CSV exports</div>
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
                @forelse($recentQuotations ?? [] as $q)
                <tr>
                  <td><a href="{{ route('quotations.show', $q) }}" style="color:#1756c8;font-weight:700">{{ $q->quotation_number }}</a></td>
                  <td>{{ $q->user->name }}</td>
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
            @foreach($companies ?? [] as $company)
            <div class="db-company-card">
              <div class="db-company-name">{{ $company->name }}</div>
              <div class="db-company-label">Total Quotations</div>
              <div class="db-company-count">{{ $company->quotations_count }}</div>
              <div class="db-progress">
                @php $maxCount = ($companies ?? collect())->max('quotations_count') ?: 1; @endphp
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
            <i class="ti ti-refresh" style="font-size:12px"></i> Refresh
          </button>
        </span>
      </div>
      <div class="db-chart-grid">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>System Activity</span></div>
          <div class="db-activity-list">
            @forelse($allActivities ?? [] as $a)
            @php
              $descLower = strtolower($a->description ?? $a->action);
              $dotColor = str_contains($descLower, 'quotation') ? '#1756c8'
                : (str_contains($descLower, 'sticker') ? '#0f7a65'
                : (str_contains($descLower, 'user') ? '#7c3aed'
                : (str_contains($descLower, 'claim') ? '#d4860e'
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
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Notifications &amp; Alerts</span></div>
          <div class="db-activity-list">
            @forelse($unreadNotifications ?? [] as $n)
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
            @if(($unreadCount ?? 0) > 5)
            <a href="{{ route('admin.notifications.index') }}" class="text-[10px] font-semibold text-blue-600 hover:underline" style="display:block;margin-top:6px;">+{{ $unreadCount - 5 }} more unread</a>
            @endif
          </div>
          <div style="height:10px"></div>
          <div class="db-activity-list">
            <div class="db-activity-item" style="margin-bottom:6px">
              <div class="db-activity-dot" style="background:#c0392b"></div>
              <div style="font-weight:600;font-size:10px;color:#7a8baa;text-transform:uppercase;letter-spacing:0.6px">Recent Claims</div>
            </div>
            @forelse($recentClaims ?? [] as $claim)
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#d4860e"></div>
              <div>
                <div class="db-activity-text"><a href="{{ route('admin.claims.show', $claim) }}" style="color:#d4860e;font-weight:700">{{ $claim->claim_number }}</a> &mdash; {{ $claim->customer_name ?? 'N/A' }}</div>
                <div class="db-activity-time">{{ $claim->created_at->diffForHumans() }}</div>
              </div>
            </div>
            @empty
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7a8baa"></div>
              <div>
                <div class="db-activity-text">No claims yet</div>
                <div class="db-activity-time">Claims will appear here</div>
              </div>
            </div>
            @endforelse
          </div>
          <a href="{{ route('admin.claims.index') }}" style="display:inline-block;margin-top:10px;font-size:10px;font-weight:600;color:#1756c8;text-decoration:none;">View all claims →</a>
        </div>
      </div>

      <div class="db-section-label">Recent Users</div>
      <div class="db-table-wrap">
        <table class="db-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentUsers ?? [] as $u)
            <tr>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td>{{ $u->roles->first()?->name ?? 'N/A' }}</td>
              <td>
                <span class="db-badge {{ $u->is_active ? 'badge-active' : 'badge-expired' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td>{{ $u->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" style="padding:32px;text-align:center;color:#7a8baa;font-size:12px">No users yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="db-chart-grid" style="margin-top:14px">
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
            quotationDates: {{ json_encode(($quotationDates ?? collect())->map(fn($d) => ['date' => $d['date'], 'count' => $d['count']])->values()) }},
            allQuotationData: {{ json_encode($quotationDates ?? collect()) }},
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
              <button @click="prevMonth()" class="p-0.5 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-[11px] font-semibold text-gray-800" x-text="monthName() + ' ' + year"></span>
              <button @click="nextMonth()" class="p-0.5 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
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

            <template x-teleport="body">
              <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(2px)">
                <div @click.away="closeModal()" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
                  <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                      <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Quotations</p>
                      <h3 class="text-sm font-bold text-gray-800 dark:text-white mt-0.5" x-text="modalDate"></h3>
                    </div>
                    <button @click="closeModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
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
          <div class="db-card-header"><span><span class="dot"></span>Recent Audit Alerts</span></div>
          <div class="db-activity-list">
            @forelse($recentAlerts ?? [] as $alert)
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

      <div class="db-section-label">Quotation Tools</div>
      <div class="action-bar">
        <span class="action-bar-label">Actions</span>
        <a href="{{ route('quotations.index') }}?export=pdf" class="action-btn action-btn-download">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download
        </a>
        <button onclick="window.print()" class="action-btn action-btn-print">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Print
        </button>
        <button @click="$el.closest('.db-root').querySelector('.pdf-preview').classList.toggle('open')" class="action-btn action-btn-preview">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          Preview
        </button>
      </div>

      <div class="pdf-preview">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div style="font-size:11px;font-weight:700;color:#1a2540">PDF Preview</div>
          <button @click="$el.closest('.pdf-preview').classList.remove('open')" style="background:none;border:none;font-size:16px;color:#7a8baa;cursor:pointer">&times;</button>
        </div>
        <iframe srcdoc="<p style='font-family:system-ui;padding:40px;text-align:center;color:#7a8baa'>Select a quotation to preview</p>"></iframe>
      </div>

      <div class="db-section-label">Compare Insurance Companies</div>
      <div class="comparison-grid">
        @foreach($companies ?? [] as $company)
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
          <div class="compare-card-rate">{{ $company->rate ?? $defaultRate ?? 6.5 }}% rate</div>
          <div class="compare-card-premium">KES {{ number_format($totalPremium > 0 ? $totalPremium / max(($companies ?? collect())->count(), 1) : 0) }}</div>
          <a href="{{ route('quotations.index') }}?company={{ urlencode($company->name) }}" class="compare-card-btn">View Quotes</a>
        </div>
        @endforeach
      </div>

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
            @foreach($companies ?? [] as $company)
            <tr>
              <td style="font-weight:700;color:#1a2540">{{ $company->name }}</td>
              <td>{{ $company->quotations_count }}</td>
              <td>KES {{ number_format($company->quotations_count > 0 ? $totalPremium / max($totalQuotations ?? 1, 1) : 0) }}</td>
              <td>{{ $company->rate ?? $defaultRate ?? 6.5 }}%</td>
              <td><span class="db-badge badge-active">Active</span></td>
            </tr>
            @endforeach
          </tbody>
          @if(($companies ?? collect())->count() > 1)
          <tfoot>
            <tr>
              <td>Total</td>
              <td>{{ $totalQuotations ?? 0 }}</td>
              <td>KES {{ number_format($totalPremium ?? 0) }}</td>
              <td>&mdash;</td>
              <td>&mdash;</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>

      <div class="db-section-label">Platform Health</div>
      <div class="db-kpi-grid" style="margin-top:0">
        @php
            $healthChecks = $healthChecks ?? collect();
            $healthTypes = ['database' => 'Database', 'site' => 'Website', 'server' => 'Server', 'sticker_api' => 'Sticker API'];
            $healthIcons = ['database' => 'ti-database', 'site' => 'ti-globe', 'server' => 'ti-server', 'sticker_api' => 'ti-api'];
        @endphp
        @foreach(['database', 'site', 'server', 'sticker_api'] as $type)
          @php
            $check = $healthChecks->firstWhere('type', $type);
            $isUp = $check && $check->status === 'up';
            $isDegraded = $check && $check->status === 'degraded';
            $accentColor = $isUp ? '#0f7a65' : ($isDegraded ? '#d4860e' : '#c0392b');
            $iconBg = $isUp ? '#e1f5ee' : ($isDegraded ? '#fef3c7' : '#fdeaea');
            $label = $healthTypes[$type] ?? ucfirst($type);
          @endphp
          <a href="{{ route('admin.health.index') }}" class="db-kpi" style="--accent:{{ $accentColor }};--icon-bg:{{ $iconBg }};cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
            <div class="db-kpi-icon"><i class="ti {{ $healthIcons[$type] }}" aria-hidden="true"></i></div>
            <div class="db-kpi-label">{{ $label }}</div>
            <div class="db-kpi-value" style="font-size:16px">{{ $check ? ucfirst($check->status) : 'Unknown' }}</div>
            <div class="db-kpi-sub" style="font-size:9px">{{ $check ? $check->message : 'Not checked' }}</div>
          </a>
        @endforeach
      </div>

    </div>
    <!-- /db-main-content -->
    </div>
    <!-- /db-layout -->
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
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
            alerts: {!! json_encode($weather["alerts"] ?? []) !!},
            pollTimer: null,
            coords: null,
            init() {
                this.detectLocation();
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
                fetch(url)
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

    var companyChart = new Chart(document.getElementById('companyChart'), {
      type: 'doughnut',
      data: {
        labels: {!! json_encode(($quotationsByCompany ?? collect())->keys()) !!},
        datasets: [{
          label: 'Quotations',
          data: {!! json_encode(($quotationsByCompany ?? collect())->values()) !!},
          backgroundColor: ['#1756c8','#7c3aed','#0f7a65','#d4860e','#c0392b','#0f7a65'],
          borderWidth: 2,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.2,
        cutout: '55%',
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 10, padding: 8, font: { size: 10 }, color: '#7a8baa' } }
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
      type: 'line',
      data: {
        labels: {!! json_encode(($monthlyQuotations ?? collect())->pluck('month')) !!},
        datasets: [{
          label: 'Quotations',
          data: {!! json_encode(($monthlyQuotations ?? collect())->pluck('total')) !!},
          borderColor: '#1756c8',
          backgroundColor: 'rgba(23,86,200,0.08)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#1756c8',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.8,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#7a8baa' } },
          x: { grid: { display: false }, ticks: { color: '#7a8baa' } }
        },
        onClick: function() {
          window.location.href = '{{ route("quotations.index") }}';
        }
      }
    });

    var distributionChart = new Chart(document.getElementById('distributionChart'), {
  type: 'bar',
  data: {
    labels: ['Generated', 'Completed'],
    datasets: [{
      label: 'Quotations',
      data: [
        {{ $statusDistribution['generated'] ?? 0 }},
        {{ $statusDistribution['completed'] ?? 0 }}
      ],
      backgroundColor: ['#1756c8', '#0f7a65'],
      borderRadius: 6,
      borderWidth: 0,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 1.8,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#7a8baa' } },
      x: { grid: { display: false }, ticks: { color: '#7a8baa' } }
    }
  }
});

var premiumChart = new Chart(document.getElementById('premiumChart'), {
  type: 'line',
  data: {
    labels: {!! json_encode(($premiumTrend ?? collect())->pluck('month')) !!},
    datasets: [{
      label: 'Avg Premium (KES)',
      data: {!! json_encode(($premiumTrend ?? collect())->pluck('avg_premium')) !!},
      borderColor: '#7c3aed',
      backgroundColor: 'rgba(124,58,237,0.08)',
      fill: true,
      tension: 0.4,
      pointBackgroundColor: '#7c3aed',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 1.8,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: false,
        grid: { color: 'rgba(0,0,0,0.04)' },
        ticks: { color: '#7a8baa', callback: v => 'KES ' + Number(v).toLocaleString() }
      },
      x: { grid: { display: false }, ticks: { color: '#7a8baa' } }
    }
  }
});

    document.addEventListener('DOMContentLoaded', function () {
      var btn = document.getElementById('dbActivityRefresh');
      var auto = document.getElementById('dbActivityAutoRefresh');
      if (!btn) return;
      var saved = localStorage.getItem('db_activity_auto_refresh');
      if (auto && saved === '1') auto.checked = true;
      var timer = null;
      function schedule() {
        if (timer) clearInterval(timer);
        if (auto && auto.checked) timer = setInterval(function () { window.location.reload(); }, 30000);
      }
      if (auto) {
        auto.addEventListener('change', function () {
          localStorage.setItem('db_activity_auto_refresh', auto.checked ? '1' : '0');
          schedule();
        });
      }
      btn.addEventListener('click', function () {
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader" style="font-size:12px;display:inline-block"></i> Refreshing...';
        window.location.reload();
      });
      schedule();
    });
    </script>
    @endpush
</x-app-layout>
