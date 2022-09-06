<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpdBillDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        'opd_billing_id', 'item_type', 'item_id', 'item_name', 'item_qty',
        'unit_tarrif',  'total_tarrif', 'discount', 'remarks', 'status'
    ];

    public function bill() {
        return $this->belongsTo(OpdBilling::class);
    }

    public function item(){
        if ($this->item_type == 'SERVICE'){
            return $this->belongsTo(OpdService::class, 'item_id', 'id');
        }elseif ($this->item_type == 'PACKAGE'){
            return $this->belongsTo(OpdServicePackage::class, 'item_id', 'id');
        }elseif ($this->item_type == 'LABTEST'){
            return $this->belongsTo(Pathology::class, 'item_id', 'id');
        }elseif ($this->item_type == 'MEDICINE'){
            return $this->belongsTo(OpdService::class, 'item_id', 'id');
        }else  {
            return $this->belongsTo(OpdService::class, 'item_id', 'id');
        }
    }


    public function processCorporateClientDiscount($request){

        $patient = Patient::find($request->patient_id);
        if ($patient->corporate_client_id){
            $corporate_client = $patient->corporate_client;
            if ($corporate_client){
                $discount = $corporate_client->checkDiscount('OPD', $this->item_type, $this->item_id);

                $discount_amount = 0;
                if ($discount){
                    if($discount->discount_amount){ $discount_amount = $discount->discount_amount; }
                    elseif($discount->discount_percentage){
                        $discount_amount = (($this->unit_tarrif * $this->item_qty) * $discount->discount_percentage) / 100;
                    }
                }

                if($discount_amount){
                    $this->discount = $discount_amount;
                    $this->total_tarrif = ($this->unit_tarrif * $this->item_qty) - $discount_amount;
                    $this->save();
                }

            }
        }
    }

}
