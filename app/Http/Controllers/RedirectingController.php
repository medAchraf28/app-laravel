<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RedirectingController extends Controller
{
    public function goToView(Request $request)
    {
        return view('redirecting');
    }

}
