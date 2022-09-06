<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function person(){
        return $this->belongsTo(Doctor::class, 'person_id', 'id');
    }

    public function bill(){
        if ($this->bill_type == 'OPD'){
            return $this->belongsTo(OpdBilling::class, 'bill_id', 'id');
        }else{
            return $this->belongsTo(IpdBilling::class, 'bill_id', 'id');
        }
    }

    public static function create_or_update_referral($bill, $bill_type, $referred_personal_id, $referral_amount){
        $check = Referral::where('bill_type', $bill_type)->where('bill_id', $bill->id)
            ->where('person_id', $referred_personal_id)->first();
        if($check){
            $check->update(['referral_amount' => $referral_amount]);
        }else{
            if ($referral_amount > 0){
                $default_params = [
                    'person_id' => $referred_personal_id,
                    'bill_type' => $bill_type,
                    'bill_id' => $bill->id,
                    'referral_amount' => $referral_amount,
                    'status' => 'PENDING',
                ];

                $bill = Referral::create($default_params);
            }

        }

        return $bill;
    }


}
