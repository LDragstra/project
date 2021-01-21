<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facturen_statuslog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $dates = [
        'datumtijd',
    ];

    protected $table = 'facturen_statuslog';
}
