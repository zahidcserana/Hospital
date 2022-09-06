<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class OpdBilling extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        'bill_no', 'patient_id', 'doctor_id', 'referred_personal_id', 'total_bill_amount', 'total_discount_amount', 'discount_type',
        'discount_value', 'refund_amount', 'refund_remarks', 'refund_status','created_by','refund_date',
        'total_paid_amount',  'total_due_amount', 'payment_status', 'payment_method',  'remarks', 'status', 'referred_personal_id'
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

   public function doctor() {
        return $this->belongsTo(Doctor::class);
    }

   public function referred_personal() {
        return $this->belongsTo(Doctor::class, 'referred_personal_id', 'id');
    }

   public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function bill_details() {
        return $this->hasMany(OpdBillDetail::class);
    }

    public function bill_items() {
        $items = $this->bill_details;
        $items = collect($items)->map(function ($item, $key) {
            $data = $item;
            $data['item'] = $item->item;
            return $data;
        });
        return $items->all();
    }

    public static function create_or_get_initial_bill($request){
        $default_params = [
            'bill_no' => mt_rand(100000, 999999),
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'referred_personal_id' => $request->referred_personal_id,
            'total_bill_amount' => 0,
            'total_discount_amount' => 0,
            'total_paid_amount' => 0,
            'total_due_amount' => 0,
            'payment_status' => 'DUE',
            'payment_method' => 'CASH',
            'created_by' => Auth::user()->id,
            'status' => 'INITIATE',
        ];
        $bill = OpdBilling::where('patient_id', $request->patient_id)->where('status', 'INITIATE')->first();
        if ($bill) return $bill;

        $bill = OpdBilling::create($default_params);
        return $bill;
    }

    public function update_bill_summery(){
        $bill = OpdBilling::find($this->id);
        $items = $bill->bill_details;

        $special_discount = 0;
        if ($bill->discount_type === "Percentage"){
            $special_discount = ($items->sum('total_tarrif') * $bill->discount_value) / 100;
        }else{
            $special_discount = $bill->discount_value;
        }

        $discount = $items->sum('discount') + $special_discount;
        $total_bill_amount = $items->sum('total_tarrif') - $special_discount;
        $bill->total_bill_amount = $total_bill_amount;
        $bill->total_discount_amount  = $discount;
        $bill->total_due_amount  = $total_bill_amount - $bill->total_paid_amount;

        $bill->save();
    }

    public function update_referral_summery(){
        $bill = OpdBilling::find($this->id);
        $referred_personal_id = $bill->referred_personal_id;
        if ( $referred_personal_id ) {
            $items = $bill->bill_details;
            $referral_amount = 0;
            foreach ($items as $item) {
                $percentage_value = 0;

                $setting = ReferralSetting::where('person_id', $referred_personal_id)
                    ->where('service_id', $item->item_id)->where('item_type', $item->item_type)->first();

                if ($setting){
                    $percentage_value = $setting->percentage_value;
                }else{
                    $setting = ReferralSetting::where('person_id', $referred_personal_id)
                        ->where('service_type', 'OPDALL')->first();
                    if($setting){
                        $percentage_value = $setting->percentage_value;
                    }
                }

                if ($percentage_value){
                    $referral_amount += (($item->total_tarrif * $percentage_value)/100);
                }
            }
            // update bill referral amount
            Referral::create_or_update_referral($bill, 'OPD', $referred_personal_id, $referral_amount);

        }
    }


    public function adjust_payment($payment_amount, $collection_type=null, $payment_status = null){
        $due_amount = $this->total_due_amount - $payment_amount;
        $this->total_paid_amount = $this->total_paid_amount + $payment_amount;
        $this->total_due_amount = $due_amount;

        if($payment_status && $payment_status=='FULL'){
            $this->payment_status = $due_amount <= 0 && $payment_status == 'FULL' ? "PAID" : "PARTIAL";
            $this->status = $due_amount <= 0 && $payment_status == 'FULL'  ? "COMPLETE" : "INCOMPLETE";
        }

        $this->save();
    }

    public function make_payment($payment_amount){

        $payment_params = [
            'payment_date' => date('Y-m-d'),
            'billing_type' => 'OPD',
            'billing_id' => $this->id,
            'payment_amount' => $payment_amount,
            'payment_method' => $this->payment_method,
            'created_by' => Auth::user()->id,
            'status' => 'PARTIAL'
        ];

        $payment = PaymentHistory::create($payment_params);
    }
}
