<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Sticker;
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
        ];

        $byCompany = Company::active()
            ->withCount('stickers')
            ->get()
            ->map(function ($c) {
                return [
                    'name' => $c->name,
                    'count' => $c->stickers_count,
                    'downloads' => Sticker::where('company_id', $c->id)->sum('download_count'),
                ];
            });

        return view('admin.stickers.index', compact('stickers', 'stats', 'byCompany'));
    }
}
