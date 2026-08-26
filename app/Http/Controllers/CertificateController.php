<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function create()
    {
        return redirect()->route('stickers.issue');
    }

    public function store(Request $request)
    {
        return redirect()->route('stickers.issue')->with('info', 'Please use the new certificate issuance form to upload your KYC documents and issue a certificate.');
    }
}
