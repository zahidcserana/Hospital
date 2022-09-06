<?php

namespace App\Http\Controllers;

use App\Models\IpdBilling;
use App\Models\OpdBilling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Inertia\Inertia;
use App\Models\PaymentHistory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentHistoryController extends Controller
{
    /**
     * Display a listing of payment history
     */
    public function index(Request $request)
    {
        $payments = PaymentHistory::where('id', '>', 0);

        if ($request->from_date && $request->to_date){
            $dateS = new Carbon($request->from_date);
            $dateE = new Carbon($request->to_date);
            $payments =  $payments->whereBetween('payment_date', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
        }

        $payments = $payments->latest()->get();

        $query = $request->query();
        if( $request->has('patient_id') or (!empty($query['search_type']) and !empty($query['search_text']))) {

            $payments = $payments->filter(function ($value, $key) use($request) {
                $query = $request->query();
                $bill_info = '';
                if ($value->billing_type == 'IPD'){
                    $bill_info = $value->ipd_billing->patient_admission;
                }else{
                    $bill_info = $value->opd_billing;
                }

                if ($request->has('patient_id')){
                    return $bill_info->patient_id==$request->query('patient_id');
                }

                if (!empty($query['search_type']) && !empty($query['search_text'])){
                    $search_type = $query['search_type'];
                    $search_text = $query['search_text'];

                    if ($search_type == 'bill_no' or $search_type=='patient_code'){
                        return $bill_info->bill_no == $search_text;
                    }
                    if ($search_type == 'patient_name'){
                        return $bill_info->patient->name == $search_text;
                    }
                    if ($search_type == 'bill_type'){
                        return $value->billing_type == $search_text;
                    }
                    if ($search_type == 'doctor_name'){
                        return $bill_info->doctor->name == $search_text;
                    }
                }


                return false;
            });

        }


        $payments = $payments->filter(function ($item, $key) {
            $data = $item;
            $bill_info = $data->bill_info();
            if ($bill_info){
                $data['patient'] = $bill_info->patient;
                $data['bill'] = $bill_info;
                $data['doctor'] = $bill_info->doctor;
                return true;
            }
            return false;
        });
        $data['total_payment'] = $payments->sum('payment_amount');

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $key = 'page';
        $actual_link = preg_replace('~(\?|&)'.$key.'=[^&]*~', '$1', $actual_link);

        $page = isset($query['page']) ? $query['page'] : 1;
        $payments = $this->paginateCollection($payments, 20, $page, ['path' => $actual_link, ]);

        $data['query'] = $query;
        $data['payments'] = $payments;

        return Inertia::render('billing/payment/index', ['data' => $data]);
    }

    public function paginateCollection($items, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (\Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : \Illuminate\Support\Collection::make($items);
        return new \Illuminate\Pagination\LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
        //ref for array_values() fix: https://stackoverflow.com/a/38712699/3553367
    }


    /**
     * Show the form for creating a new payment history
     */
    public function create(Request $request)
    {
        if( $request->has('patient_id') ) {
            $data['patient_id'] = $request->query('patient_id');
        }
        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name.' / '.$item->mobile.' / code-'.$item->code;
            return $item;
        });
        $data['patients'] = $patients;

        return Inertia::render('billing/payment/create', ['data' => $data]);
    }

    /**
     * Supplier a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'billing_type' => ['required'],
            'billing_id' => ['required'],
            'payment_amount' => ['required'],
            'payment_method' => ['required'],
            'status' => ['required'],
        ])->validate();

        // adjust bill payment amount
        if($request->billing_type == 'OPD'){
            $bill = OpdBilling::find($request->billing_id);
        }else{
            $bill = IpdBilling::find($request->billing_id);
        }
        $bill->adjust_payment($request->payment_amount, $request->collection_type, $request->status);

        $data = $request->all();
        $data['payment_date'] = date('Y-m-d');
        $data['created_by'] = Auth::user()->id;;
        PaymentHistory::create($data);

        return redirect()->back()
            ->with('message', 'Payment Created Successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentHistory $paymentHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = PaymentHistory::find($id);
        $bill_info = $data->bill_info();
        $data['patient_id'] = $bill_info->patient_id;

        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name.' / '.$item->mobile.' / id-'.$item->id;
            return $item;
        });
        $data['patients'] = $patients;

        return Inertia::render('billing/payment/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'billing_type' => ['required'],
            'billing_id' => ['required'],
            'payment_amount' => ['required'],
            'payment_method' => ['required'],
            'status' => ['required'],
        ])->validate();

        $paymentHistory = PaymentHistory::find($request->id);
        // find payment amount difference between two payment
        $amount_difference =  $request->payment_amount - $paymentHistory->payment_amount;

        // adjust bill payment amount
        if($request->billing_type == 'OPD'){
            $bill = OpdBilling::find($request->billing_id);
        }else{
            $bill = IpdBilling::find($request->billing_id);
        }
        $bill->adjust_payment($amount_difference, $request->collection_type, $request->status);

        if ($request->has('id')) {
            PaymentHistory::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Payment Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $payment = PaymentHistory::find($request->input('id'));

            // adjust bill payment amount
            if($payment->billing_type == 'OPD'){
                $bill = OpdBilling::find($payment->billing_id);
            }else{
                $bill = IpdBilling::find($payment->billing_id);
            }
            $bill->adjust_payment(-$payment->payment_amount);

            $payment->delete();
            return redirect()->back();
        }
    }


    public function receipt($payment_id){
        $payment = PaymentHistory::find($payment_id);
        $bill_info = $payment->bill_info();
        $payment['patient'] = $bill_info->patient;
        $payment['created_by_user'] = $payment->created_by_user;
        if ($payment->billing_type == 'IPD'){
            $payment['bill'] = $payment->ipd_billing;
        }else{
            $payment['bill'] = $bill_info;
        }
        return response()->json($payment, Response::HTTP_OK);
    }

    public function my_summery(){
        $data['users'] = User::all();
        return Inertia::render('billing/summery/index', ['data' => $data]);
    }

    public function get_my_summery(Request $request){
        $user = Auth::user();
        if ($request->employee_id){

            if ($request->employee_id == 'all'){
                $history =  PaymentHistory::where('id', '>', '0');
                $opd_refund = OpdBilling::where('refund_amount', '>', 0);
                $ipd_refund = IpdBilling::where('refund_amount', '>', 0);
            }

            if ($request->employee_id != 'all'){
                $history =  PaymentHistory::where('created_by', $request->employee_id);
                $opd_refund = OpdBilling::where('refund_amount', '>', 0)->where('refund_by', $request->employee_id);
                $ipd_refund = IpdBilling::where('refund_amount', '>', 0)->where('refund_by', $request->employee_id);
            }

            if ($request->from_date && $request->to_date){
                $dateS = new Carbon($request->from_date);
                $dateE = new Carbon($request->to_date);

                $history =  $history->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
                $opd_refund = $opd_refund->whereBetween('refund_date', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
                $ipd_refund = $ipd_refund->whereBetween('refund_date', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
            }

//            if ($request->to_date){
//                $to_date = $request->to_date." 23:59:59";
//                $history =  $history->whereDate('created_at','<=', $to_date);
//                $opd_refund = $opd_refund->whereDate('refund_date','<=', $to_date);
//                $ipd_refund = $ipd_refund->whereDate('refund_date','<=', $to_date);
//            }

            $history = $history->get();
        }else{
//            if ($request->summery_type == 'today'){
//                $history =  PaymentHistory::whereDate('created_at', Carbon::today())->where('created_by', $user->id)->get();
//                $opd_refund = OpdBilling::where('refund_amount', '>', 0)->where('refund_by', $user->id)->whereDate('refund_date', Carbon::today());
//                $ipd_refund = IpdBilling::where('refund_amount', '>', 0)->where('refund_by', $user->id)->whereDate('refund_date', Carbon::today());
//            }else{
                $dateS = new Carbon($request->from_date);
                $dateE = new Carbon($request->to_date);

                $history =  PaymentHistory::whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"])->get();
                $opd_refund = OpdBilling::whereBetween('refund_date', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
                $ipd_refund = IpdBilling::whereBetween('refund_date', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
//            }
        }

        $history = $history->map(function ($item, $key) {
            $data = $item;
            $bill_info = $data->bill_info();
            $data['patient'] = $bill_info->patient;
            $data['bill'] = $bill_info;
            return $data;
        });
        $user['summery'] =$history;
        $user['total'] =$history->sum('payment_amount');
        $user['refund_amount'] =$opd_refund->sum('refund_amount') + $ipd_refund->sum('refund_amount');
        return response()->json($user, Response::HTTP_OK);
    }

}
