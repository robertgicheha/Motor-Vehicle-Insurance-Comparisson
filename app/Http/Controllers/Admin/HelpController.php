<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class HelpController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::with('user')->latest()->paginate(20);
        $openCount = Enquiry::where('status', 'open')->count();
        $inProgressCount = Enquiry::where('status', 'in_progress')->count();
        $resolvedCount = Enquiry::where('status', 'resolved')->count();
        return view('admin.help.index', compact('enquiries', 'openCount', 'inProgressCount', 'resolvedCount'));
    }

    public function show($id)
    {
        $enquiry = Enquiry::with('user')->findOrFail($id);
        return view('admin.help.show', compact('enquiry'));
    }

    public function updateStatus(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $request->validate(['status' => 'required|in:open,in_progress,resolved']);
        $enquiry->update(['status' => $request->status]);
        return back()->with('success', 'Enquiry status updated.');
    }

    public function respond(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $request->validate(['response' => 'required|string']);

        if ($enquiry->user_id) {
            $user = User::find($enquiry->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\EnquiryResponse($enquiry, $request->response));
            }
        }

        $enquiry->update(['status' => 'resolved']);

        \App\Services\AuditService::activity('enquiry_responded', "Responded to enquiry #{$enquiry->id}");

        return back()->with('success', 'Response sent and enquiry marked as resolved.');
    }
}
