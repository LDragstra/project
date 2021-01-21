<?php

namespace App\Services;

use App\Bon;
use App\BonStatus;
use App\Factor;
use App\Factuur;
use Auth;
use DB;
use Illuminate\Support\Facades\Cache;

class Snelstart
{
    private $cron;
    private $snelstart;

    public function __construct($cron = 0)
    {
        $this->cron = $cron;

        $this->snelstart = new \Snelstart\Snelstart();

        $this->getToken();
    }

    public function getToken()
    {
        $this->getCacheId();

        $this->snelstart->setApiKey(config('services.snelstart.key'));

        return $this->snelstart->generateAccessToken(config('services.snelstart.COMPANY_' . session('company')['id']));
    }

    public function getCacheId()
    {
        if ($this->cron) {
            session(['company' => ['id' => $this->cron]]);
        }

        if (!session('company')['id']) {
            session(['company' => Auth::user()->getBedrijfsData]);
        }

        return session('company')['id'];
    }

    public function addInvoice($data)
    {
        $data = json_encode($data);

        $snelstartVerkooporder = $this->snelstart->send('verkooporders', 'post', $data);

        return $snelstartVerkooporder['id'];
    }

    public function deleteInvoiceOrder(Factuur $factuur)
    {
        if ($factuur->snelstart_id) {
            $this->snelstart->send('verkooporders/' . $factuur->snelstart_id, 'delete');
        }
        DB::transaction(
            function () use ($factuur) {
                $bon = Bon::find($factuur->bon_id);
                $bon->status = 1;
                $bon->update();
                $bonStatus = BonStatus::where('bon_id', $factuur->bon_id)->get();
                foreach ($bonStatus as $status) {
                    $status->delete();
                }
                $factoring = Factor::where('factuur', $factuur->id)->first()->delete();

                $factuur->delete();
            }
        );
    }

    public function sendReceipt($data)
    {
        $data = json_encode($data);
        $this->snelstart->send('documenten/Verkoopboekingen', 'post', $data);
    }

    public function totalNumbers($startDay, $endDay)
    {
        return $this->getData('rapportages/periodebalans?start=' . $startDay . '&end=' . $endDay, 3600 * 40);
    }

    public function getBonData()
    {
        $relaties_first500 = $this->getData(
            'relaties?$orderby=Naam asc&$top=500&$filter=Relatiesoort/any(r:r eq \'Klant\')',
            3600 * 40
        );

        $relaties2_second500 = $this->getData(
            'relaties?$orderby=Naam asc&$skip=500&$filter=Relatiesoort/any(r:r eq \'Klant\')',
            3600 * 40
        );

        $relationsCombined = array_merge($relaties_first500, $relaties2_second500);

        return [
            $relationsCombined,
            $this->getData('verkoopordersjablonen', 114000),
            $this->getData('artikelen', 3600 * 40 * 52)
        ];
    }

    public function getData($type, $seconds, $sort = null)
    {
        if (Cache::has($type . $this->getCacheId())) {
            return Cache::get($type . $this->getCacheId());
        }
        Cache::put($type . $this->getCacheId(), $this->snelstart->send($type . $sort, 'get'), $seconds);
        return Cache::get($type . $this->getCacheId());
    }

    public function getRevenue(array $grootboeken)
    {
        $numbers = [
            'omzet' => [],
            'kosten' => []
        ];
        foreach ($grootboeken as $grootboek) {
            if (($grootboek['grootboekNummer'] >= '4000' && $grootboek['grootboekNummer'] <= '7000') || ($grootboek['grootboekNummer'] >= '9000' && $grootboek['grootboekNummer'] <= '9999')) {
                $numbers['kosten'][$grootboek['grootboekNummer']] = (intval($grootboek['debet']) - intval(
                        $grootboek['credit']
                    ));
            }
            if ($grootboek['grootboekNummer'] >= '8000' && $grootboek['grootboekNummer'] < '9000') {
                $numbers['omzet'][$grootboek['grootboekNummer']] = (intval($grootboek['debet']) - intval(
                        $grootboek['credit']
                    ));
            }
        }
        return $numbers;
    }

    public function getRow($type, $sort = null)
    {
        return $this->snelstart->send($type . $sort, 'get');
    }


}
