<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  
    <div class="db-root fade-in">
      <h2 class="sr-only">MIC Global Risks Motor Insurance Admin Dashboard</h2>

      <div class="db-top-bar">
        <div class="db-greeting">
          <h1>{{ $greeting }}, {{ auth()->user()->first_name ?: auth()->user()->name }}!</h1>
          <p>Admin Dashboard &mdash; Motor Insurance Portal</p>
        </div>
         @if(isset($weather))
        @php
            $weatherEmoji = match(true) {
                str_contains(strtolower($weather['condition']), 'clear')        => '☀️',
                str_contains(strtolower($weather['condition']), 'cloud')        => str_contains($weather['icon'], '02') ? '⛅' : '☁️',
                str_contains(strtolower($weather['condition']), 'rain')         => '🌧️',
                str_contains(strtolower($weather['condition']), 'drizzle')      => '🌦️',
                str_contains(strtolower($weather['condition']), 'thunderstorm') => '⛈️',
                str_contains(strtolower($weather['condition']), 'snow')         => '❄️',
                str_contains(strtolower($weather['condition']), 'fog')          => '🌫️',
                str_contains(strtolower($weather['condition']), 'mist')         => '🌫️',
                str_contains(strtolower($weather['condition']), 'haze')         => '🌫️',
                str_contains(strtolower($weather['condition']), 'smoke')        => '🌫️',
                str_contains(strtolower($weather['condition']), 'dust')         => '🌪️',
                str_contains(strtolower($weather['condition']), 'sand')         => '🌪️',
                str_contains(strtolower($weather['condition']), 'tornado')      => '🌪️',
                str_contains(strtolower($weather['condition']), 'squall')       => '💨',
                default                                                          => '🌤️',
            };
        @endphp
        <div class="db-weather">
          <span style="font-size:36px;line-height:1;display:flex;align-items:center;">{{ $weatherEmoji }}</span>
          <div>
            <p class="temp">
              {{ $weather['temperature'] }}°C
              <span style="font-size:11px;font-weight:500;color:#7a8baa">Feels {{ $weather['feels_like'] }}°C</span>
            </p>
            <p class="cond">
              {{ $weather['description'] }} &middot; {{ $weather['city'] }}
              &middot; {{ $weather['humidity'] }}% RH
              @if(!empty($weather['wind_speed'])) &middot; {{ $weather['wind_speed'] }} m/s wind @endif
            </p>
            @if(!empty($weather['local_time']) || !empty($weather['uvi']))
            <p style="font-size:10px;color:#7a8baa;margin:2px 0 0;font-weight:500;">
              @if(!empty($weather['local_time'])) {{ $weather['local_time'] }} @endif
              @if(!empty($weather['sunrise'])) &middot; ☀️ {{ $weather['sunrise'] }} @endif
              @if(!empty($weather['sunset'])) &nbsp;🌙 {{ $weather['sunset'] }} @endif
              @if(!empty($weather['uvi'])) &middot; UV {{ $weather['uvi'] }} @endif
            </p>
            @endif
            @if(!empty($weather['alerts']))
              @foreach($weather['alerts'] as $alert)
              <p style="font-size:10px;font-weight:700;color:#c0392b;margin:3px 0 0;">
                ⚠️ {{ $alert['event'] }} · {{ $alert['start'] }} – {{ $alert['end'] }}
              </p>
              @endforeach
            @endif
          </div>
        </div>
        @endif
      </div>

      <div class="db-section-label">Key Metrics</div>
      <div class="db-kpi-grid">
        <div class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc">
          <div class="db-kpi-icon"><i class="ti ti-users" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Users</div>
          <div class="db-kpi-value">{{ $totalUsers }}</div>
          <div class="db-kpi-trend trend-up"><i class="ti ti-trending-up" style="font-size:12px"></i> +8% this month</div>
        </div>
        <div class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e1f5ee">
          <div class="db-kpi-icon"><i class="ti ti-wifi" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Online Now</div>
          <div class="db-kpi-value">{{ $onlineUsers }}</div>
          <div class="db-kpi-sub">Active sessions</div>
        </div>
        <div class="db-kpi" style="--accent:#7c3aed;--icon-bg:#ede9fe">
          <div class="db-kpi-icon"><i class="ti ti-file-description" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Quotations</div>
          <div class="db-kpi-value">{{ $totalQuotations }}</div>
          <div class="db-kpi-trend trend-up"><i class="ti ti-trending-up" style="font-size:12px"></i> +12% vs last month</div>
        </div>
        <div class="db-kpi" style="--accent:#d4860e;--icon-bg:#fef3c7">
          <div class="db-kpi-icon"><i class="ti ti-coins" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Total Premium (KES)</div>
          <div class="db-kpi-value" style="font-size:20px">{{ number_format($totalPremium) }}</div>
          <div class="db-kpi-trend trend-up"><i class="ti ti-trending-up" style="font-size:12px"></i> +5.3% growth</div>
        </div>
      </div>

      <div class="db-kpi-grid" style="margin-top:14px">
        <div class="db-kpi" style="--accent:#c0392b;--icon-bg:#fdeaea">
          <div class="db-kpi-icon"><i class="ti ti-car" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Vehicles Insured</div>
          <div class="db-kpi-value">{{ $totalQuotations }}</div>
          <div class="db-kpi-sub">Across all policies</div>
        </div>
        <div class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e1f5ee">
          <div class="db-kpi-icon"><i class="ti ti-ticket" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Stickers Issued</div>
          <div class="db-kpi-value">{{ $totalQuotations }}</div>
          <div class="db-kpi-trend trend-neutral"><i class="ti ti-minus" style="font-size:12px"></i> Stable</div>
        </div>
        <div class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc">
          <div class="db-kpi-icon"><i class="ti ti-mail-forward" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Emails Dispatched</div>
          <div class="db-kpi-value">{{ $totalQuotations }}</div>
          <div class="db-kpi-sub">PDFs attached</div>
        </div>
        <div class="db-kpi" style="--accent:#7c3aed;--icon-bg:#ede9fe">
          <div class="db-kpi-icon"><i class="ti ti-shield-check" aria-hidden="true"></i></div>
          <div class="db-kpi-label">Conversion Rate</div>
          <div class="db-kpi-value">68%</div>
          <div class="db-kpi-trend trend-up"><i class="ti ti-trending-up" style="font-size:12px"></i> +3% this week</div>
        </div>
      </div>

      <div class="db-section-label">Period Breakdown</div>
      <div class="db-period-grid">
        <div class="db-period-card">
          <div class="db-period-label">Today</div>
          <div class="db-period-value">{{ $todayQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </div>
        <div class="db-period-card amber">
          <div class="db-period-label">This Week</div>
          <div class="db-period-value">{{ $weekQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </div>
        <div class="db-period-card teal">
          <div class="db-period-label">This Month</div>
          <div class="db-period-value">{{ $monthQuotations }}</div>
          <div class="db-period-desc">quotations generated</div>
        </div>
      </div>

      <div class="db-section-label">Quick Actions</div>
      <div class="db-kpi-grid" style="margin-top:0">
        <a href="{{ route('admin.users.create') }}" class="db-kpi" style="--accent:#1756c8;--icon-bg:#e8f0fc;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-user-plus" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Add User</div>
          <div class="db-kpi-sub">Create a new user account</div>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="db-kpi" style="--accent:#0f7a65;--icon-bg:#e1f5ee;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-report" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Generate Reports</div>
          <div class="db-kpi-sub">Export quotation data</div>
        </a>
        <a href="{{ route('admin.companies.create') }}" class="db-kpi" style="--accent:#d4860e;--icon-bg:#fef3c7;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-building-plus" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">Add Company</div>
          <div class="db-kpi-sub">Register a new insurer</div>
        </a>
        <a href="{{ route('quotations.index') }}" class="db-kpi" style="--accent:#7c3aed;--icon-bg:#ede9fe;cursor:pointer;text-decoration:none;display:flex;flex-direction:column;">
          <div class="db-kpi-icon"><i class="ti ti-file-description" aria-hidden="true"></i></div>
          <div class="db-kpi-label" style="margin-bottom:0">View Quotations</div>
          <div class="db-kpi-sub">Browse all quotations</div>
        </a>
      </div>

      <div class="db-section-label">Analytics</div>
      <div class="db-chart-grid">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Quotations by Company</span></div>
          <canvas id="companyChart" height="180"></canvas>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Monthly Trend</span></div>
          <canvas id="monthlyChart" height="180"></canvas>
        </div>
      </div>

      <div class="db-section-label">Activity &amp; Alerts</div>
      <div class="db-chart-grid">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>System Activity</span></div>
          <div class="db-activity-list">
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
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>My Activity</span></div>
          <div class="db-activity-list">
            @forelse($myActivities as $a)
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7c3aed"></div>
              <div>
                <div class="db-activity-text">{{ $a->description ?? $a->action }}</div>
                <div class="db-activity-time">{{ $a->created_at->diffForHumans() }}</div>
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

      <div class="db-chart-grid" style="margin-top:14px">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Quotation Calendar</span></div>
          <div x-data="{
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            selectedDate: null,
            selectedItems: [],
            quotationDates: {{ json_encode($quotationDates->map(fn($d) => ['date' => $d['date'], 'count' => $d['count']])->values()) }},
            allQuotationData: {{ json_encode($quotationDates) }},
            daysInMonth() { return new Date(this.year, this.month + 1, 0).getDate(); },
            firstDay() { return new Date(this.year, this.month, 1).getDay(); },
            monthName() { return new Date(this.year, this.month).toLocaleString('default', { month: 'long' }); },
            hasQuotations(day) {
              const dateStr = this.year + '-' + String(this.month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
              return this.quotationDates.some(d => d.date === dateStr);
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
            selectDay(day) {
              if (this.hasQuotations(day)) {
                this.selectedDate = day;
                this.selectedItems = this.getQuotations(day);
              }
            }
          }">
            <div class="flex items-center justify-between mb-3">
              <button @click="prevMonth()" class="p-1 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <span class="text-xs font-semibold text-gray-800" x-text="monthName() + ' ' + year"></span>
              <button @click="nextMonth()" class="p-1 text-gray-400 hover:text-gray-600 transition" style="background:none;border:none;cursor:pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            <div class="grid grid-cols-7 gap-0.5 text-center mb-1">
              <template x-for="d in ['S','M','T','W','T','F','S']" :key="d">
                <div class="text-[10px] font-semibold text-gray-400 py-1" x-text="d"></div>
              </template>
            </div>
            <div class="grid grid-cols-7 gap-0.5 text-center">
              <template x-for="blank in firstDay()" :key="'b' + blank">
                <div></div>
              </template>
              <template x-for="day in daysInMonth()" :key="day">
                <div>
                  <div @click="selectDay(day)"
                    :class="{
                      'bg-blue-600 text-white shadow-sm': selectedDate === day,
                      'hover:bg-gray-100': selectedDate !== day,
                      'cursor-pointer': hasQuotations(day),
                      'cursor-default': !hasQuotations(day)
                    }"
                    class="relative w-full aspect-square flex items-center justify-center text-[11px] font-medium text-gray-700 rounded-lg transition">
                    <span x-text="day"></span>
                    <template x-if="hasQuotations(day)">
                      <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full border border-white"></span>
                    </template>
                  </div>
                </div>
              </template>
            </div>
            <template x-if="selectedDate && selectedItems.length > 0">
              <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-[10px] font-semibold text-gray-500 mb-2">
                  Quotations on <span x-text="selectedDate + ' ' + monthName() + ' ' + year"></span>:
                </p>
                <template x-for="(dayData, idx) in selectedItems" :key="idx">
                  <div class="space-y-1">
                    <template x-for="(item, iidx) in dayData.items" :key="iidx">
                      <template x-if="iidx === 0 || dayData.count <= 1">
                        <a x-bind:href="'{{ url('quotations') }}/' + item.id" class="flex items-center justify-between py-1 px-2 rounded hover:bg-gray-50 text-[11px] text-gray-700 no-underline">
                          <span>
                            <span class="font-semibold text-blue-600" x-text="item.quotation_number"></span>
                            <span class="text-gray-500" x-text="' — ' + item.user_name"></span>
                          </span>
                          <span class="text-gray-400 text-[10px]" x-text="item.company"></span>
                        </a>
                      </template>
                    </template>
                    <template x-if="dayData.count > 1">
                      <a x-bind:href="'{{ route('quotations.index') }}' + '?date=' + dayData.date" class="block py-1 text-[10px] font-semibold text-blue-600 hover:underline text-center">
                        <span x-text="'1 + ' + (dayData.count - 1) + ' more'"></span>
                      </a>
                    </template>
                  </div>
                </template>
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
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#3b82f6"></div>
              <div>
                <div class="db-activity-text">{{ $n->data['message'] ?? $n->data['action'] ?? 'Notification' }}</div>
                <div class="db-activity-time">{{ $n->created_at->diffForHumans() }}</div>
              </div>
            </div>
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

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
    Chart.defaults.font.family = "'Montserrat', sans-serif";
    Chart.defaults.font.size = 11;

    new Chart(document.getElementById('companyChart'), {
      type: 'bar',
      data: {
        labels: {!! json_encode($quotationsByCompany->keys()) !!},
        datasets: [{
          label: 'Quotations',
          data: {!! json_encode($quotationsByCompany->values()) !!},
          backgroundColor: ['#1756c8','#7c3aed','#0f7a65'],
          borderRadius: 8,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#7a8baa' } },
          x: { grid: { display: false }, ticks: { color: '#7a8baa' } }
        }
      }
    });

    new Chart(document.getElementById('monthlyChart'), {
      type: 'line',
      data: {
        labels: {!! json_encode($monthlyQuotations->pluck('month')) !!},
        datasets: [{
          label: 'Quotations',
          data: {!! json_encode($monthlyQuotations->pluck('total')) !!},
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
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#7a8baa' } },
          x: { grid: { display: false }, ticks: { color: '#7a8baa' } }
        }
      }
    });
    </script>
    @endpush
</x-app-layout>