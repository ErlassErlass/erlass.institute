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
}
