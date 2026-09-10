<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function faq()
    {
        return view('pages.faq');
    }

    public function policy()
    {
        return view('pages.policy');
    }
}