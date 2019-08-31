<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonorLeft extends Model
{
    protected $table = "donor1";

    function right(){
        return $this->belongsTo("App\DonorRight","seqno","seqno");
    }
}
