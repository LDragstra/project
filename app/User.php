<?php

namespace App;

use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $guarded = [];

    protected $table = 'medewerkers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->passwordhash;
    }

    public function updateBedrijfsData($id)
    {
        $this->bedrijfsnr = $id;
        $this->update();
        session()->forget('company');
        session(['company' => Auth::user()->getBedrijfsData]);
    }

    public function getBedrijfsData()
    {
        return $this->belongsTo(Bedrijf::class, 'bedrijfsnr', 'id');
    }
}
