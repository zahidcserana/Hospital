<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'billing_type', 'billing_id', 'payment_amount', 'payment_method', 'payment_date',
        'status', 'remarks', 'created_by', 'updated_by'
    ];

    public function opd_billing(){
        return $this->belongsTo(OpdBilling::class, 'billing_id', 'id');
    }

    public function ipd_billing(){
        return $this->belongsTo(IpdBilling::class, 'billing_id', 'id');
    }

    public function created_by_user(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function bill_info(){
        if ($this->billing_type == 'OPD'){
            return $this->opd_billing ? $this->opd_billing : null;
        }else{
            return $this->ipd_billing ? $this->ipd_billing->patient_admission : null;
        }
    }



}
