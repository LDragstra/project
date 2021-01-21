<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bedrijf extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'bedrijven';
}
