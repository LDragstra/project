<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'projecten';

    public function getProjectSoort()
    {
        return $this->aanneem_dagtarief;
    }

    public function berekenProjectGefactureerd($soort, $uren = 0)
    {
        $gefactureerd = floatval($this->getInvoices->sum('bedrag'));
        if (!$gefactureerd || $gefactureerd == 0) {
            $gefactureerd = 1;
            $percentage = 100;
            $soortProject += ['factuur' => 'Er is nog geen factuur gemaakt'];
        }
        $brutowinst = [];
        $percentage = 0;
        $totaalUren = 0;
        $omzet = [
            'omzet' => 0,
            'soortProject' => '',
            'details' => []
        ];
        $details = [];

        if ($uren === 0) {
            $details = array_merge($details, ['Er zijn geen uren geboekt op dit project']);
        } else {
            foreach ($uren as $uur) {
                $teBerekenenUren = $uur->getUren();
                if ($teBerekenenUren <= 0) {
                    $teBerekenenUren = 8;
                }
                $totaalUren += round($teBerekenenUren);
            }
            if ($totaalUren < 8 && $soort == 1) {
                $totaalUren = 8;
            }
        }

        $omzet = array_merge($omzet, $this->berekenOmzet($soort, $details, $totaalUren));

        $percentage = round($gefactureerd / $omzet['omzet'] * 100);

        if (array_key_exists('meerwerk', $omzet)) {
            $brutowinst = array_merge($brutowinst, $omzet);
        }
        $brutowinst = array_merge($brutowinst, [
            'gefactureerd' => $gefactureerd,
            'omzet' => $omzet['omzet'],
            'omzetBerekening' => $omzet['omzetBerekening'],
            'afrondingFactureren' => $omzet['omzet'] - $gefactureerd,
            'percentageOmzet' => $percentage,
            'soortProject' => $omzet['soortProject'],
            'details' => $omzet['details'],
            'tijd' => $totaalUren
        ]);

        return $brutowinst;
    }

    protected function berekenOmzet($soort, array $details = [], $totaalUren = 0)
    {
        if ($totaalUren == 0) {
            $totaalUren = 1;
        };

        switch ($soort) {
        case 0:
            $omzet['soortProject'] = 'Aanneemsom';
            $meerwerk = DB::table('meerwerk')->where('projectid', $this->id)->get();
            if (!$this->aanneemsom || $this->aanneemsom == 0) {
                $this->aanneemsom = 1;
                $omzet['details'] = array_merge($details, ['Er is geen aanneemsom bekend voor dit project']);
            }
            $omzet['omzet'] = $this->aanneemsom;
            $omzet['omzetBerekening'] = $this->aanneemsom . ' aanneemsom';

            if ($meerwerk->count() >= 1) {
                foreach ($meerwerk as $extra) {
                    $omzet['meerwerk'][] = $extra;
                    $omzet['omzet'] += $extra->bedrag;
                }
                $omzet['omzetBerekening'] = $this->aanneemsom . ' aanneemsom' . ' + ' . ($omzet['omzet'] - $this->aanneemsom) . ' meerwerk';
            }

            return $omzet;
        break;
        case 1:
            $omzet['soortProject'] = 'Dagtarief';
            if (!$this->dagtarief || $this->dagtarief == 0) {
                $omzet['details'] += array_merge($details, ['Er is geen dagtarief aangegeven']);
                $this->dagtarief = 1;
            }

            $omzet['omzet'] = ($totaalUren / 8) * $this->dagtarief;
            $omzet['omzetBerekening'] = $totaalUren . ' : 8 uur ' . 'x ' . $this->dagtarief . '(dagtarief)';
            return $omzet;
        break;
        case 2:
            $omzet['soortProject'] = 'Uurtarief';
            if (!$this->uurtarief || $this->uurtarief == 0) {
                $omzet['details'] += array_merge($details, ['Er is geen uurtarief aangegeven']);
                $this->uurtarief = 1;
            }
            $omzet['omzet'] = $totaalUren * $this->uurtarief;
            $omzet['omzetBerekening'] = $totaalUren . ' uur x €' . $this->uurtarief . ' per uur';
            return $omzet;
        break;
    }
    }

    public function getCompany()
    {
        return $this->belongsTo(Bedrijf::class, 'soort', 'soort');
    }

    public function getClient()
    {
        return $this->belongsTo(Klant::class, 'klantid', 'id');
    }

    public function getInvoices()
    {
        return $this->hasMany(Factuur::class, 'projectnr', 'id')->orderBy('id', 'desc');
    }
}
