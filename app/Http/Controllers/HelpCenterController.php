<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    /**
     * Display the Help Center / How-To 101 & FAQ page.
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * Display the Admin Guide & SOP page (Admin, Admin Sistem, Webmaster only).
     */
    public function adminGuide()
    {
        if (! auth()->user()?->hasAdminAccess()) {
            abort(403, 'Akses khusus Administrator.');
        }

        return view('admin.guide.index');
    }
}
