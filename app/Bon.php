<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bon extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'bonnen';

    public function scopeGetBonOmTeZetten($query)
    {
        $query->where('status', 1)->orderBy('id', 'desc');
    }

    public function getKlantFromApi($array, $businessName)
    {
        foreach ($array as $check) {
            $snelstart_businessname = $check['naam'];
            $businessName = $businessName;
            similar_text($snelstart_businessname, $businessName, $percent);
            if ($percent >= 75) {
                return $check['id'];
            }
        }
    }

    public function scopeGetTotalsCompany($query, $bedrijf)
    {
        return $query->where('status', 1)->whereHas(
            'getProject',
            function ($query) use ($bedrijf) {
                $query->where('soort', '=', $bedrijf);
            }
        )->count();
    }

    public function scopeGetTotals($query)
    {
        return $query->where('status', 1)->count();
    }

    public function getKlantFromBon()
    {
        return $this->belongsTo(Klant::class, 'klantid', 'id');
    }

    public function getProject()
    {
        return $this->hasOne(Project::class, 'id', 'projectnr');
    }

    public function getBonStatus()
    {
        return $this->hasOne(BonStatus::class, 'bon_id', 'id');
    }
}
