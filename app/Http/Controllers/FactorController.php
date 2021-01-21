<?php

namespace App\Http\Controllers;

use App\Factor;
use App\Http\Requests\FactoringFirstPaymentRequest;
use App\Http\Requests\FactoringLastPaymentRequest;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FactorController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $uitTeKeren = Factor::where(
            [
                ['initieel', '=', 0]
            ]
        )->count();

        $uitbetalingen = Factor::where(
            [
                ['initieel', '>', '0'],
            ]
        )->count();

        $resterend = Factor::whereHas(
            'getFactuur',
            function ($query) {
                $query->where('voldaan', '=', 'J');
            }
        )->where('rest', '=', '0')->count();

        return view('pages.factoring', compact('uitTeKeren', 'resterend', 'uitbetalingen'));
    }

    public function factoringPayments()
    {
        $uitbetalingen = Factor::with('getFactuur', 'getFactuur.getProject', 'getFactuur.getProject.getClient')->where(
            [
                ['initieel', '>', '0']
            ]
        )->orderBy('datum', 'desc')->paginate(15);

        return view('pages.factoringPayments', compact('uitbetalingen'));
    }

    /**
     * @return Application|Factory|View
     */
    public function factoringPayment()
    {
        $companyPerc = session('company')['factorPercentage'];

        $percentage = '0.' . $companyPerc;

        $uitTeKerenFacturen = Factor::with(
            'getFactuur',
            'getFactuur.getProject',
            'getFactuur.getProject.getClient'
        )->where(
            [
                ['initieel', '=', 0]
            ]
        )->get();

        return view('pages.factoringPayment', compact('uitTeKerenFacturen', 'percentage'));
    }

    /**
     * @param Request $request
     */
    public function factoringPaid(FactoringFirstPaymentRequest $request): void
    {
        $factor = Factor::find($request->id);
        $factor->initieel = $request->amount;
        $factor->nummer = $request->internNummer;
        $factor->datum = now();
        $factor->update();
    }

    /**
     * @return Application|Factory|View
     */
    public function factoringRestPayment() : view
    {
        $restUitkering = Factor::with(
            'getFactuur',
            'getFactuur.getProject',
            'getFactuur.getProject.getClient'
        )->whereHas(
            'getFactuur',
            function ($query) {
                $query->where('voldaan', '=', 'J');
            }
        )->where('rest', '=', '0')->get();

        return view('pages.factoringRestPayment', compact('restUitkering'));
    }

    /**
     * @param FactoringLastPaymentRequest $validated
     */
    public function factoringRestPaid(FactoringLastPaymentRequest $validated): void
    {
        $factor = Factor::find($validated->id);
        $factor->rest = $validated->amount;
        $factor->datum = now();
        $factor->update();
    }

    /**
     * @param Factor $id
     * @return JsonResponse
     */
    public function destroy(Factor $id)
    {
        try {
            $id->delete();
        } catch (Exception $error) {
            return response()->json(['msg' => $error]);
        }
    }

}
