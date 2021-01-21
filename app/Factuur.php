<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Factuur extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'facturen';

    protected $dates = [
        'datum',
        'voldaanop',
        'created_at',
    ];

    protected $email;

    /**
     * @return BelongsTo
     */
    public function getBon()
    {
        return $this->belongsTo(Bon::class, 'bon_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function getProject()
    {
        return $this->belongsTo(Project::class, 'projectnr', 'id');
    }

    /**
     * @return HasMany
     */
    public function getLogs()
    {
        return $this->hasMany(Facturen_statuslog::class, 'factuur_id', 'id');
    }

    public function getFactor(): BelongsTo
    {
        return $this->belongsTo(Factor::class, 'id', 'factuur');
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        if ($this->getProject->getClient && $this->getProject->getClient->email) {
            $email = $this->getProject->getClient->email;
        } else {
            $email = config('mail.from.address');
        }

        if (DB::table('mailqueue_excluded')->where('email', '=', $email)->first()) {
            $email .= '(excluded)';
        }

        return $email;
    }

    /**
     * @param $data
     * @param $snelstart_id
     */
    public function create($data, $snelstart_id)
    {
        $this->projectnr = $data['projectId'];
        $this->datum = $data['datum'];
        $this->soort = 0;
        $this->omschr = $data['omschrijving'];
        $this->bedrag = $data['factuurbedrag'];
        $this->openstaandSaldo = $data['factuurbedrag'];
        $this->nummer = 0;
        $this->krediet = $data['krediettermijn'];
        $this->fl = 0;
        $this->snelstart_id = $snelstart_id;
        $this->bon_id = $data['bonId'];
        $this->voldaan = 'N';
        $this->keer = 0;
        $this->____afst = '';
        $this->map = '';
        $this->fact = '';
        $this->save();

        if ($data['fl'] == 'Ja') {
            $factor = new Factor();
            $factor->factuur = $this->id;
            $factor->save();
        }

        $klant = Klant::find($this->getBon->getKlantFromBon->id);
        $klant->merge = $data['merge'];
        $klant->snelStart = $data['klant'];
        $klant->update();
    }

    /**
     * @param $query
     * @param $id
     */
    public function scopeAllProjectInvoices($query, $id)
    {
        $query->where('projectnr', $id);
    }

    /**
     * @param $query
     */
    public function scopeVapInvoices($query)
    {
        $query->where('snelstart_id', '!=', '');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSnelStartOrder($id)
    {
        return (new Snelstart())->getRow('verkooporders/' . $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getSnelStartInvoice($id)
    {
        return (new Snelstart())->getRow('verkoopfacturen/' . $id);
    }

}
