<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralPaymentHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function person(){
        return $this->belongsTo(Doctor::class, 'person_id', 'id');
    }

}
