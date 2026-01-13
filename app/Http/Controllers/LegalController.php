<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function termsAndConditions()
    {
        return view('legal.terms');
    }

    public function privacyPolicy()
    {
        return view('legal.privacy');
    }

    public function disclaimer()
    {
        return view('legal.disclaimer');
    }

    public function cookiePolicy()
    {
        return view('legal.cookies');
    }

    public function refundPolicy()
    {
        return view('legal.refund');
    }
}
