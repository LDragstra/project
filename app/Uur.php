<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Uur extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'log_medewerker';

    protected $dates = [
    ];

    public function scopeProject($query, $id)
    {
        return $query->where('projectnr', $id);
    }

    public function getUren()
    {
        return ($this->datumtot - $this->datumvan) / 3600;
    }

    public function getWorkingDate()
    {
        return Carbon::createFromDate($this->jaar, $this->maand, $this->dag);
    }


}
