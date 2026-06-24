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

      <div class="db-chart-grid" style="margin-top:14px">
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Recent Activity</span></div>
          <div class="db-activity-list">
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#1756c8"></div>
              <div>
                <div class="db-activity-text">New quotation QT-2024/0042 generated for Toyota Corolla</div>
                <div class="db-activity-time">2 minutes ago · John Kamau</div>
              </div>
            </div>
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#0f7a65"></div>
              <div>
                <div class="db-activity-text">Sticker SK-2024/0018 issued &amp; emailed to client</div>
                <div class="db-activity-time">14 minutes ago · Jane Muthoni</div>
              </div>
            </div>
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#d4860e"></div>
              <div>
                <div class="db-activity-text">GA Insurance rate updated to 4.1% by admin</div>
                <div class="db-activity-time">1 hour ago · Admin</div>
              </div>
            </div>
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#7c3aed"></div>
              <div>
                <div class="db-activity-text">New user account created for sales team</div>
                <div class="db-activity-time">3 hours ago · System</div>
              </div>
            </div>
            <div class="db-activity-item">
              <div class="db-activity-dot" style="background:#c0392b"></div>
              <div>
                <div class="db-activity-text">Quotation QT-2024/0039 expired — no sticker issued</div>
                <div class="db-activity-time">Yesterday · Auto-system</div>
              </div>
            </div>
          </div>
        </div>
        <div class="db-card">
          <div class="db-card-header"><span><span class="dot"></span>Quick Stats</span></div>
          <div class="db-quick-stats">
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">Avg. Premium</div>
              <div class="db-quick-stat-value">KES 48,200</div>
            </div>
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">Avg. Vehicle Value</div>
              <div class="db-quick-stat-value">KES 1.05M</div>
            </div>
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">GA Insurance</div>
              <div class="db-quick-stat-value">4.1% rate</div>
            </div>
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">Sanlam Allianz</div>
              <div class="db-quick-stat-value">6.5% rate</div>
            </div>
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">Pending Stickers</div>
              <div class="db-quick-stat-value">7</div>
            </div>
            <div class="db-quick-stat">
              <div class="db-quick-stat-label">Emails Failed</div>
              <div class="db-quick-stat-value">2</div>
            </div>
          </div>
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