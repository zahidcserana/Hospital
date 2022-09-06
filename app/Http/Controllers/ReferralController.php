<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Referral;
use App\Models\ReferralPaymentHistory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{

    /**
     * PatientAdmission list
     *
     */
    public function referralStatement(Request $request)
    {
        $query = $request->query();

        $referrals = Referral::select('person_id', DB::raw('sum(referral_amount) as total_referral_amount'))
            ->groupBy('person_id');

        if(isset($query['person_id'])){
            $referrals = $referrals->where('person_id', '=', $query['person_id']);
        }

        if (isset($query['from_date'])){
            $referrals = $referrals->whereDate('created_at', '>=', $query['from_date']);
        }

        if (isset($query['to_date'])){
            $referrals = $referrals->whereDate('created_at', '<=', $query['to_date']);
        }

        $referrals = $referrals->get();

        $referrals = $referrals->map(function ($item, $key) {
            $data = $item;
            $data['payment_amount'] = ReferralPaymentHistory::where('person_id', $item->person_id)->sum('payment_amount');
            $data['person'] = $data->person;
            return $data;
        });

        $doctors = Doctor::all();
        $doctors = $doctors->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });

        $param['query'] = $query;
        $param['doctors'] = $doctors;
        $param['data'] = $referrals;

        return Inertia::render('referralSetting/referralStatement', ['param' => $param]);
    }


    // return person referral summery
    public function referralSummery(Request $request){

        $referral_summery = Referral::where('person_id', $request->person_id)->sum('referral_amount');
        $payment_amount = ReferralPaymentHistory::where('person_id', $request->person_id)->sum('payment_amount');

        $data['referral_amount'] = $referral_summery;
        $data['payment_amount'] = $payment_amount;
        $data['person'] = Doctor::find($request->person_id);

        return response()->json($data, Response::HTTP_OK);

    }

    // return person payment summery
    public function paymentHistory(Request $request){

        $referral_summery = Referral::where('person_id', $request->person_id)->sum('referral_amount');
        $payment_amount = ReferralPaymentHistory::where('person_id', $request->person_id)->sum('payment_amount');
        $histories = ReferralPaymentHistory::where('person_id', $request->person_id)->get();

        $data['referral_amount'] = $referral_summery;
        $data['payment_amount'] = $payment_amount;
        $data['histories'] = $histories;
        $data['person'] = Doctor::find($request->person_id);

        return response()->json($data, Response::HTTP_OK);

    }

    // return person referral history
    public function referralHistory(Request $request){

        $referral_summery = Referral::where('person_id', $request->person_id)->sum('referral_amount');
        $payment_amount = ReferralPaymentHistory::where('person_id', $request->person_id)->sum('payment_amount');
        $histories = Referral::where('person_id', $request->person_id)->get();

        $histories = $histories->map(function ($item, $key) {
            $item['bill'] = $item->bill;
            return $item;
        });

        $data['referral_amount'] = $referral_summery;
        $data['payment_amount'] = $payment_amount;
        $data['histories'] = $histories;
        $data['person'] = Doctor::find($request->person_id);

        return response()->json($data, Response::HTTP_OK);

    }

    // return person referral summery
    public function makePayment(Request $request){

//        $referral_summery = Referral::where('person_id', $request->person_id)->sum('referral_amount');
//        $payment_amount = ReferralPaymentHistory::where('person_id', $request->person_id)->sum('payment_amount');

        ReferralPaymentHistory::create(['person_id' => $request->person_id,
            'payment_amount' => $request->payment_amount,
            'created_by' => Auth::user()->id]);

        $data['message'] = 'Payment Added Successfully.';

        return Redirect::route('referralStatement')->with('message', 'Payment Added Successfully.');
//        return response()->json($data, Response::HTTP_OK);

    }


}
