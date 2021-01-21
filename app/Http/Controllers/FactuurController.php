<?php

namespace App\Http\Controllers;

use App\Bon;
use App\Factuur;
use App\Http\Requests\AddInvoiceRequest;
use App\Mailqueue;
use App\Services\Snelstart;
use Artisan;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class FactuurController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $facturen = Factuur::whereHas(
            'getProject',
            function ($query) {
                $query->where('soort', '=', session('company')['soort']);
            }
        )->with('getLogs', 'getProject', 'getProject.getClient')->orderBy('id', 'desc')->paginate(10, ['*'], 'pagina');

        return view('pages.facturen', compact('facturen'));
    }

    /**
     * @param Bon $bon
     */
    public function done(Bon $bon): void
    {
        $bon->status = 2;
        $bon->update();
    }

    /**
     * @param Factuur $factuur
     * @return Application|Factory|View
     */
    public function show(Factuur $factuur)
    {
        $projectFacturen = Factuur::allProjectInvoices($factuur->getProject->id)->where(
            'id',
            '!=',
            $factuur->id
        )->orderBy(
            'id',
            'desc'
        )->get();

        return view('pages.factuur', compact('factuur', 'projectFacturen'));
    }

    /**
     * @param $id
     * @param Snelstart $snelstart
     * @return JsonResponse
     */
    public function delete($id, Snelstart $snelstart)
    {
        try {
            $snelstart->deleteInvoiceOrder($id);
            return response()->json(
                ['msg' => 'Verkooporder in Snelstart verwijderd, factuur in vap verwijderd en de bon teruggezet naar nog te factureren.']
            );
        } catch (Throwable $th) {
            return response()->json(['msg' => 'Niets verwijderd. Gaat niet goed.' . $th]);
        }
    }

    /**
     * @param Request $request
     * @param Snelstart $snelstart
     * @param Factuur $factuur
     */
    public function addInvoiceOrder(AddInvoiceRequest $factuurData, Snelstart $snelstart, Factuur $factuur)
    {
        $data = $factuurData->data;

        $vapData = $factuurData->vapData;

        $snelstart_invoice = $snelstart->addInvoice($data);

        $factuur->create($vapData, $snelstart_invoice);
    }

    /**
     * @return mixed
     */
    public function getInvoiceNumber(Snelstart $snelstart)
    {
        return $factuurnummer = $snelstart->getRow(
            'verkoopfacturen',
            '?$orderby=FactuurDatum desc,Factuurnummer desc&$top=1&$select=Factuurnummer'
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function mail(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'factuurId' => 'required',
                'email' => 'required|email:rfc'
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $mail = (new Mailqueue())->setQueue($request->email, $request->factuurId, $request->text);

        return response()->json(['msg' => 'Factuur is verzonden']);
    }

}
