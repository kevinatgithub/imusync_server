<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonorFinal extends Model
{
    protected $table = "donor";
    public $primaryKey = "seqno";
    public $incrementing = false;
    public $timestamps = false;

    function left(){
        return $this->belongsTo("App\DonorLeft",'seqno','seqno');
    }

    function right(){
        return $this->belongsTo("App\DonorRight",'seqno','seqno');
    }
}
