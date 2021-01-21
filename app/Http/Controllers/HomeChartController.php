<?php

namespace App\Http\Controllers;

use App\Services\Charts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeChartController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $thisYear = date('Y');
        $lastYear = $thisYear - 1;

        $chartData = (new Charts())->getPeriodNumbers($lastYear, $thisYear);

        return response()->json(
            ['lastYear' => $lastYear, 'thisYear' => $thisYear, 'periods' => $thisYear, 'chartData' => $chartData]
        );
    }
}
