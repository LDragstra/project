<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonStatus extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'bonnen_statuslog';


    public function createStatus($id, $bedrag)
    {
        $this->create([
            'bon_id' => $id,
            'bedrag' => $bedrag,
            'toelichtingbon' => 'Factuur is aangemaakt en klaargezet in Snelstart',
            'door_klant' => 0,
            'status' => 3,
            'bon_nr' => 0
        ]);
    }

    public function getBon()
    {
        return $this->belongsTo(Bon::class, 'id', 'bon_id');
    }
}
