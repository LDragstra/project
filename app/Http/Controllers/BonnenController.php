<?php

namespace App\Http\Controllers;

use App\Bon;
use App\BonStatus;
use App\Http\Requests\MarkReceipeRequest;
use App\Services\Snelstart;
use Artisan;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BonnenController extends Controller
{
    /**
     * Display a list of open invoice receipes
     *
     * @param Snelstart $snelstart
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function index(Snelstart $snelstart)
    {
        $bonnen = Bon::with('getKlantFromBon', 'getProject')->getBonOmTeZetten()->whereHas(
            'getProject',
            function ($query) {
                $query->where('soort', '=', session('company')['soort']);
            }
        )->paginate(15);

        [$relaties, $sjablonen, $artikelen] = $snelstart->getBonData();

        return view('pages.bonnen', compact('bonnen', 'relaties', 'sjablonen', 'artikelen'));
    }


    /**
     *  Let the user clear cache.
     *  Needed when data like new customers, articles or templates have been made in Snelstart
     *
     * @return string
     */
    public function cache() : string
    {
        Artisan::call('cache:clear');
        return "Data is gewist! Pagina wordt vernieuwd in 5 seconden.";
    }

    /**
     * @param MarkReceipeRequest $receipe
     */
    public function markReceiptAsDone(MarkReceipeRequest $receipe) : void
    {
        DB::transaction(function() use ($receipe){
            $bon = Bon::find($receipe->id);
            $bon->status = 2;
            $bon->update();
            (new Bonstatus())->createStatus($receipe->id, $bon->bon);
        });

        Cache::forget('bonnen_' . session('company')['id']);
    }
}
