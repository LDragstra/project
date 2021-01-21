<?php

namespace App\Http\Controllers;

use Auth;

class SwitchCompanyController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $id)
    {
        Auth::user()->updateBedrijfsData($id);

        return redirect()->back();
    }
}
