<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateDocument;
use App\Models\Company;
use App\Models\Sticker;
use App\Models\StickerEmailLog;
use App\Services\AuditService;
use Illuminate\Http\Request;

class StickerAdminController extends Controller
{
    public function index()
    {
        $stickers = Sticker::with('company')->latest()->paginate(20);

        $stats = [
            'total' => Sticker::count(),
            'today' => Sticker::whereDate('created_at', today())->count(),
            'this_week' => Sticker::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Sticker::whereMonth('created_at', now()->month)->count(),
            'total_downloads' => Sticker::sum('download_count'),
            'emails_sent' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'sent')->count(),
            'emails_failed' => StickerEmailLog::where('type', 'outbound_to_underwriter')->whereIn('status', ['failed', 'bounced'])->count(),
            'pending_responses' => Sticker::where('status', 'issued')->whereNull('response_received_at')->count(),
        ];

        $byCompany = Company::active()
            ->withCount('stickers')
            ->get()
            ->map(function ($c) {
                return [
                    'name' => $c->name,
                    'count' => $c->stickers_count,
                ];
            });

        $recentEmails = StickerEmailLog::with(['sticker', 'triggeredBy'])
            ->where('type', 'outbound_to_underwriter')
            ->latest()
            ->limit(10)
            ->get();

        $trend = $this->buildTrend();

        return view('admin.stickers.index', compact('stickers', 'stats', 'byCompany', 'recentEmails', 'trend'));
    }

    /**
     * Zero-filled daily sticker count for the last 14 days, for the trend chart.
     */
    protected function buildTrend(): array
    {
        $rows = Sticker::selectRaw('DATE(created_at) as day, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $labels = [];
        $counts = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels[] = $day->format('d M');
            $counts[] = (int) ($rows[$day->toDateString()]->count ?? 0);
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    public function tracker(Request $request)
    {
        $view = $request->get('view', 'all');

        $query = Sticker::with(['company', 'issuedBy'])->latest();

        if ($view === 'mine') {
            $query->where('issued_by', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('sticker_number', 'like', "%{$search}%")
                  ->orWhere('underwriter_sticker_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereLikeCI('vehicle_registration', $search);
            });
        }

        $stickers = $query->paginate(20)->appends($request->query());
        $companies = Company::active()->get();
        $statuses = ['pending' => 'Pending', 'issued' => 'Issued', 'completed' => 'Completed'];

        $byCompany = Company::active()
            ->withCount(['stickers' => fn ($q) => $view === 'mine' ? $q->where('issued_by', auth()->id()) : $q])
            ->get()
            ->filter(fn ($c) => $c->stickers_count > 0)
            ->map(fn ($c) => ['name' => $c->name, 'count' => $c->stickers_count])
            ->sortByDesc('count')
            ->values();

        $myCount = Sticker::where('issued_by', auth()->id())->count();
        $allCount = Sticker::count();

        return view('admin.stickers.tracker', compact('stickers', 'companies', 'statuses', 'view', 'byCompany', 'myCount', 'allCount'));
    }

    public function updateStickerForm(Request $request)
    {
        $stickers = Sticker::with(['company', 'issuedBy', 'completedBy'])
            ->where('status', 'issued')
            ->latest()
            ->paginate(20);

        return view('admin.stickers.update-stickers', compact('stickers'));
    }

    public function searchSticker(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2',
        ]);

        $search = trim($request->search);
        $sticker = Sticker::with(['company', 'issuedBy', 'quotation'])
            ->whereRaw('LOWER(sticker_number) = ?', [strtolower($search)])
            ->orWhereRaw('LOWER(underwriter_sticker_no) = ?', [strtolower($search)])
            ->first();

        if (!$sticker) {
            return response()->json(['success' => false, 'error' => 'Sticker not found. Search by Sticker ID (ours) or Sticker Number (from the underwriter).']);
        }

        $documents = CertificateDocument::where('sticker_id', $sticker->id)->get();

        return response()->json([
            'success' => true,
            'sticker' => [
                'id' => $sticker->id,
                'sticker_number' => $sticker->sticker_number,
                'underwriter_sticker_no' => $sticker->underwriter_sticker_no,
                'customer_name' => $sticker->customer_name,
                'vehicle_registration' => $sticker->vehicle_registration,
                'car_make' => $sticker->car_make,
                'car_model' => $sticker->car_model,
                'car_year' => $sticker->car_year,
                'premium' => $sticker->premium,
                'cover_type' => $sticker->cover_type,
                'status' => $sticker->status,
                'company' => $sticker->company->name ?? 'N/A',
                'issued_by' => $sticker->issuedBy?->name ?? 'N/A',
                'completed_by' => $sticker->completedBy?->name,
                'completed_at' => $sticker->generated_at?->format('d M Y H:i'),
                'is_completed' => $sticker->status === 'completed',
                'documents' => $documents->pluck('document_type', 'scan_status'),
            ],
        ]);
    }

    public function updateSticker(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required|exists:stickers,id',
            'underwriter_sticker_no' => 'required|string|max:100',
        ]);

        $sticker = Sticker::findOrFail($request->sticker_id);
        $user = auth()->user();

        if ($sticker->status === 'completed') {
            return response()->json([
                'success' => false,
                'error' => 'This sticker has already been completed by ' . ($sticker->completedBy?->name ?? 'Unknown') . ' on ' . $sticker->generated_at?->format('d M Y H:i') . '. It cannot be overridden.',
            ]);
        }

        $oldStatus = $sticker->status;
        $sticker->update([
            'status' => 'completed',
            'underwriter_sticker_no' => $request->underwriter_sticker_no,
            'generated_at' => now(),
            'completed_by' => $user->id,
        ]);

        AuditService::log('update', 'Sticker', $sticker->id, [
            'status' => $oldStatus,
            'completed_by' => null,
            'generated_at' => null,
        ], [
            'status' => 'completed',
            'completed_by' => $user->id,
            'generated_at' => now()->toISOString(),
        ]);
        AuditService::activity('sticker_completed', "Sticker {$sticker->sticker_number} marked as completed by {$user->name}. Certificate generated.");

        return response()->json([
            'success' => true,
            'message' => "Sticker {$sticker->sticker_number} marked as completed successfully.",
        ]);
    }

    public function settings()
    {
        $companies = Company::active()->get();
        return view('admin.stickers.settings', compact('companies'));
    }
}
