<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class IpdBilling extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        'bill_no', 'patient_admission_id', 'total_advance_amount', 'total_bill_amount', 'total_discount_amount',
        'total_paid_amount',  'total_due_amount', 'payment_status',  'remarks', 'status', 'discount_type',
        'discount_value', 'refund_amount', 'refund_remarks', 'refund_status', 'created_by', 'refund_date'
    ];

    public function patient_admission() {
        return $this->belongsTo(PatientAdmission::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function bill_details() {
        return $this->hasMany(IpdBillDetail::class);
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

    public function bill_active_items() {
        $instance = $this->hasMany(IpdBillDetail::class);
        $instance->getQuery()->where('status','=', 'ACTIVE');
        return $instance;
    }

    public static function create_or_get_initial_bill($request){
        $default_params = [
            'bill_no' => mt_rand(100000, 999999),
            'patient_admission_id' => $request->patient_admission_id,
            'pharmacy_invoice_number' => $request->pharmacy_invoice_number,
            'total_bill_amount' => 0,
            'total_discount_amount' => 0,
            'total_paid_amount' => 0,
            'total_due_amount' => 0,
            'total_advance_amount' => 0,
            'payment_status' => 'DUE',
            'created_by' => Auth::user()->id,
            'status' => 'INITIATE',
        ];
        $bill = IpdBilling::where('patient_admission_id', $request->patient_admission_id)->where('status', 'INITIATE')->first();
        if ($bill) return $bill;

        $bill = IpdBilling::create($default_params);
        return $bill;
    }

    public function check_due_limit($new_item){

        $patient_admission = PatientAdmission::find($this->patient_admission_id);
        // get all admission of a patient
        $all_admission = PatientAdmission::where('patient_id', $patient_admission->patient_id)
            ->pluck('id')->toArray();

        $items = $this->bill_details->where('item_id', '!=', $new_item['item_id']);
        $total_paid_amount = IpdBilling::whereIn('patient_admission_id', $all_admission)->sum('total_paid_amount');
        $total_previous_bill = $items->sum('total_tarrif');
        $previous_due = $total_previous_bill - $total_paid_amount;

        $bill_item = $this->bill_details->where('item_id', $new_item['item_id'])
            ->where('item_type', $new_item['item_type'])->first();

        if($bill_item){
            // when qty is reducing let them do

            if ($new_item['item_qty'] == 1){
                $current_bill_price = $new_item['unit_tarrif'] * ($new_item['item_qty'] + $bill_item->item_qty);
            }elseif ($new_item['item_qty'] < $bill_item->item_qty){
                return true;
            }
            $current_bill_price = $new_item['unit_tarrif'] * ($new_item['item_qty'] + $bill_item->item_qty);

        }else{
            $current_bill_price = $new_item['unit_tarrif'] * $new_item['item_qty'];
        }

        if (($previous_due +  $current_bill_price) > 10000){
            return false;
        }
        return true;

    }


    public function update_bill_summery(){
        $bill = IpdBilling::find($this->id);
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
        $bill = IpdBilling::find($this->id);
        $referred_personal_id = $bill->patient_admission->referred_personal_id;
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
                        ->where('service_type', 'IPDALL')->first();
                    if($setting){
                        $percentage_value = $setting->percentage_value;
                    }
                }

                if ($percentage_value){
                    $referral_amount += (($item->total_tarrif * $percentage_value)/100);
                }
            }
            // update bill referral amount
            Referral::create_or_update_referral($bill, 'IPD', $referred_personal_id, $referral_amount);

        }
    }


    public function adjust_payment($payment_amount, $collection_type=null, $payment_status = null){
        if ($collection_type == 'Advance'){
            $this->total_advance_amount = $this->total_advance_amount + $payment_amount;
        }

        $due_amount = $this->total_due_amount - $payment_amount;
        $this->total_paid_amount = $this->total_paid_amount + $payment_amount;
        $this->total_due_amount = $due_amount;

        if($payment_status && $payment_status=='FULL'){
            $this->payment_status = $due_amount <= 0 && $payment_status == 'FULL' ? "PAID" : "PARTIAL";
            $this->status = $due_amount <= 0 && $payment_status == 'FULL'  ? "COMPLETE" : "INCOMPLETE";
        }

        $this->save();
    }


    public function make_payment($request){

        $payment_params = [
            'payment_date' => date('Y-m-d'),
            'billing_type' => 'IPD',
            'billing_id' => $this->id,
            'payment_amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'created_by' => Auth::user()->id,
            'status' => 'PARTIAL'
        ];

        $payment = PaymentHistory::create($payment_params);
    }

    public function calculate_hourly_bill($request){

        $minutes = (strtotime($request->end_time) - strtotime($request->start_time)) / 60;
        return $minutes;

        $period = new \DatePeriod(
            new \DateTime($request->start_time),
            new \DateInterval('P1D'),
            new \DateTime($request->end_time)
        );

        $hours = 0;
        $total_date = 0;
        $dates = array();

        foreach ($period as $key => $value) {
            $total_date += 1;
            $date = $value->format('Y-m-d');
            array_push($dates, $date);
        }

        if ($total_date == 1){
            $hours = $this->get_hour_difference($request->start_time, $request->end_time);
        }elseif ($total_date >= 2){
            $count = 0;
            foreach ($dates as $key => $value) {

                if ($count == 0){
                    $date = date('Y-m-d', strtotime($dates[$count])).' 23:59:59';
                    $hours += $this->get_hour_difference($request->start_time, $date);
                } elseif (($total_date - $count) == 1){
                    $date = date('Y-m-d', strtotime($dates[$count])).' 00:00:00';
                    $hours += $this->get_hour_difference($date, $request->end_time);
                }elseif (($total_date - $count) != 0){
                    $date = date('Y-m-d', strtotime($dates[$count])).' 00:00:00';
                    $edate = date('Y-m-d', strtotime($dates[$count])).' 23:59:59';
                    $hours += $this->get_hour_difference($date, $edate);
                }

                $count += 1;
            }
        }

        $qty = 0;
        if ($hours <= 6){
            $qty = 0.5;
        }elseif ($hours > 6){
            $divide = 23;
            $qty = (int)($hours / $divide);
            $reiming_hour = (float)($hours / $divide);
            $reiming_hour = ($reiming_hour - (int)$reiming_hour)*$divide;
            if ($reiming_hour > 0 and $reiming_hour <= 6){
                $qty += 0.5;
            }elseif($reiming_hour > 6){
                $qty += 1;
            }
        }else{
            $qty = 0;
        }

        return $qty;
    }

    public function get_hour_difference($start, $end){
        $hourdiff = round((strtotime($end) - strtotime($start))/3600, 1);
        $start_hour = date("H", strtotime($start));
        $end_hour = date("H", strtotime($end));

        if ($start_hour < 12 and $end_hour > 15){
            $hourdiff = $hourdiff - 3;
        }elseif ($end_hour >= 12 and $end_hour <= 15){
            $hourdiff = $hourdiff - ( $end_hour - 12);
        }elseif ($start_hour >= 12 and $start_hour <= 15){
            $hourdiff = $hourdiff - (15 - $start_hour);
        }
        return $hourdiff;
    }


}
