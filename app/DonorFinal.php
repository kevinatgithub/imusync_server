<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonorFinal extends Model
{
    protected $table = "donor";
    public $primaryKey = "seqno";
    public $timestamps = false;
}
