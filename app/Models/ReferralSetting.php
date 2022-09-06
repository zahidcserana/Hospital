<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function person(){
        return $this->belongsTo(Doctor::class, 'person_id', 'id');
    }

    public function item(){
        if ($this->service_type == 'OPD'){
            if ($this->item_type == 'SERVICE'){
                return $this->belongsTo(OpdService::class, 'service_id', 'id');
            }else{
                return $this->belongsTo(OpdServicePackage::class, 'service_id', 'id');}
        }else{
            if ($this->item_type == 'SERVICE') {
                return $this->belongsTo(IpdService::class, 'service_id', 'id');
            }else{
                return $this->belongsTo(IpdServicePackage::class, 'service_id', 'id');
            }
        }
    }


}
