<?php

namespace App\Http\Controllers;

class HomepageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function __invoke()
    {
        return view('pages.home');
    }
}
