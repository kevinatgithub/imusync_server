<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonorRight extends Model
{
    protected $table = "donor2";

    function left(){
        return $this->belongsTo("App\DonorLeft","seqno","seqno");
    }
}
