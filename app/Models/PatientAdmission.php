<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class PatientAdmission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'patient_id', 'hospital_id', 'doctor_id', 'referred_personal_id', 'bill_no', 'floor', 'package_id', 'created_by',
        'room',  'bed', 'admitted_from',  'description', 'admission_date', 'release_date', 'referred_by', 'under_package', 'status'
    ];

    protected $guarded = [];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function hospital() {
        return $this->belongsTo(Hospital::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }

    public function referred_personal() {
        return $this->belongsTo(Doctor::class, 'referred_personal_id', 'id');
    }

    public function package() {
        return $this->belongsTo(IpdServicePackage::class, 'package_id', 'id');
    }

    public function bill() {
        return $this->hasOne(IpdBilling::class);
    }


    public function process_admission_with_package($request){
        $package = IpdServicePackage::where('id', $request->package_id)->first();
        $package_amount = $package ? $package->tariff : 0;
        $default_params = [
            'bill_no' => $this->bill_no,
            'patient_admission_id' => $this->id,
            'total_bill_amount' => $package_amount,
            'total_discount_amount' => 0,
            'total_paid_amount' => $request->advance_payment_amount,
            'total_due_amount' => $package_amount - $request->advance_payment_amount,
            'total_advance_amount' => $request->advance_payment_amount,
            'payment_status' => 'DUE',
            'created_by' => Auth::user()->id,
            'status' => 'INCOMPLETE',
        ];
        $bill = IpdBilling::create($default_params);

        if ($package){
            // create bill items
            $bill_item_params = [
                'item_qty' => 1,
                'unit_tarrif' => $package->tariff,
                'total_tarrif' => $package->tariff,
                'discount' => 0,
                'item_type' => 'PACKAGE',
                'item_id' => $package->id,
                'item_name' => $package->name,
                'status' => 'ACTIVE'
            ];
            $bill_item = $bill->bill_details()->create($bill_item_params);
        }

        $payment_params = [
            'payment_date' => date('Y-m-d'),
            'billing_type' => 'IPD',
            'billing_id' => $bill->id,
            'payment_amount' => $request->advance_payment_amount,
            'payment_method' => $request->payment_type ? $request->payment_type : 'Cash',
            'created_by' => Auth::user()->id,
            'status' => 'PARTIAL'
        ];

        $payment = PaymentHistory::create($payment_params);

        return $payment;
    }

}
