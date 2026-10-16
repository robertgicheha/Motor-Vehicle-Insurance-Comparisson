<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use App\Notifications\NewEnquiry;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        $enquiries = auth()->user()->enquiries()->latest()->get();
        return view('user.help.index', compact('enquiries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $enquiry = Enquiry::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'open',
        ]);

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewEnquiry($enquiry));
        }

        return back()->with('success', 'Your enquiry has been submitted. We will get back to you shortly.');
    }
}
