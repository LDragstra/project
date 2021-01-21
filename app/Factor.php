<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factor extends Model
{
    public $timestamps = false;

    protected $dates = ['datum'];

    protected $guarded = [];

    protected $table = 'factoring';

    /**
     * @return BelongsTo
     */
    public function getFactuur() : BelongsTo
    {
        return $this->belongsTo(Factuur::class, 'factuur', 'id');
    }

    /**
     * @return string
     */
    public function getUniqueNumber($soort)
    {
        return $soort . date('dm');
    }

}
