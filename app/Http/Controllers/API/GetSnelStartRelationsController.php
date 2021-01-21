<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Snelstart;

class GetSnelStartRelationsController extends Controller
{
    public function __invoke(Snelstart $snelStart)
    {
        return $snelStart->getRelaties();
    }
}
