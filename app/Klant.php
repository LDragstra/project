<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Klant extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'klanten';
}
