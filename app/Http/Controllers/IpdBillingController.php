<?php

namespace App\Http\Controllers;

use App\Models\IpdBillDetail;
use App\Models\IpdBilling;
use App\Models\IpdService;
use App\Models\IpdServicePackage;
use App\Models\Medicine;
use App\Models\OpdBilling;
use App\Models\Pathology;
use App\Models\PatientAdmission;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IpdBillingController extends Controller
{
    /**
     * Bill listing
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = array();
        $where = $this->getQuery($where, $query);

        $result = IpdBilling::with('patient_admission.patient', 'patient_admission.doctor')
            ->where('status', '!=', 'INITIATE')
            ->where($where);

        if (!empty($query['search_type']) && $query['search_type'] != 'bill_no' && !empty($query['search_text'])){
            $result = $result->whereHas('patient_admission', function ($query) use ($request) {
                $params = $request->query();
                if ($params['search_type'] == 'patient_code'){
                    $query = $query->where('bill_no', '=' , $params['search_text']);
                }
                return $query;
            });
        }

        if (!empty($query['search_type']) && $query['search_type'] != 'bill_no' && !empty($query['search_text'])){
            $result = $result->whereHas('patient_admission.patient', function ($query) use ($request) {
                $params = $request->query();
                if ($params['search_type'] == 'patient_mobile'){
                    $query = $query->where('patients.mobile', '=' , $params['search_text']);
                }elseif ($params['search_type'] == 'patient_id'){
                    $query = $query->where('patients.id', '=' , $params['search_text']);
                }elseif ($params['search_type'] == 'patient_name'){
                    $query = $query->where('patients.name', 'LIKE', '%' . $params['search_text'] . '%');
                }
                return $query;
            });


            $result = $result->whereHas('patient_admission.doctor', function ($query) use ($request) {
                $params = $request->query();
                if ($params['search_type'] == 'doctor_id') {
                    $query = $query->where('doctors.id', '=', $params['search_text']);
                } elseif ($params['search_type'] == 'doctor_name') {
                    $query = $query->where('doctors.name', 'LIKE', '%' . $params['search_text'] . '%');
                }
                return $query;
            });
        }

        $full_result = $result->get();
        $result = $result->latest()->paginate(20);

        $data['query'] = $query;
        $data['total_bill_amount'] = $full_result->sum('total_bill_amount');
        $data['total_discount_amount'] = $full_result->sum('total_discount_amount');
        $data['total_advance_amount'] = $full_result->sum('total_advance_amount');
        $data['total_paid_amount'] = $full_result->sum('total_paid_amount');
        $data['total_due_amount'] = $full_result->sum('total_due_amount');
        $data['data'] = $result;

        return Inertia::render('billing/ipdBilling/index', ['data' => $data]);
    }

    // search query builder
    public function getQuery($where, $query)
    {
        if (!empty($query['search_type']) && $query['search_type'] == 'bill_no' && !empty($query['search_text'])) {
            $where = array_merge(array(['ipd_billings.bill_no', $query['search_text']]), $where);
        }

        if (!empty($query['from_date'])) {
            $where = array_merge(array(['ipd_billings.created_at', '>=', $query['from_date']]), $where);
        }

        if (!empty($query['to_date'])) {
            $where = array_merge(array(['ipd_billings.created_at', '<=', $query['to_date']]), $where);
        }

        return $where;
    }

    /**
     * register new bill
     */
    public function create(Request $request)
    {
        if( $request->has('patient_id') ) {
            $data['patient_id'] = $request->query('patient_id');
        }
        $admissions = PatientAdmission::with('patient', 'bill')->latest()->get();

        $admissions = $admissions->filter(function ($value, $key) {
            if ($value->bill && $value->bill->status == 'COMPLETE'){
                return false;
            }
            return true;
        });

        $admissions = $admissions->map(function ($item, $key) {
            $item['text'] = $item->patient->name.' / '.$item->patient->mobile.' / code-'.$item->bill_no;
            return $item;
        });

        $list = [];
        foreach ($admissions as $admission){
            array_push($list, $admission);
        }

        $admissions =  $list;

        $data['admissions'] = $admissions;

        return Inertia::render('billing/ipdBilling/create', ['data' => $data]);
    }

    /**
     * store new bill
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'patient_admission_id' => ['required'],
            'payment_status' => ['required'],
            'status' => ['required'],
        ])->validate();

        $data = $request->only(['patient_admission_id', 'payment_status', 'status', 'remarks']);

        $bill = IpdBilling::where('patient_admission_id', $request->patient_admission_id)->first();

        $items = $bill->bill_details;


        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;

        $special_discount = 0;
        if ($request->discount_type === "Percentage"){
            $special_discount = ($items->sum('total_tarrif') * $request->discount_value) / 100;
        }else{
            $special_discount = $request->discount_value;
        }

        $discount = $items->sum('discount') + $special_discount;

//        $data['total_advance_amount'] = $request->payment_amount;

        $data['total_bill_amount'] = $items->sum('total_tarrif') - $special_discount;

        $data['total_discount_amount']  = $discount;

        $data['total_due_amount']  = $data['total_bill_amount'] - $bill->total_paid_amount;

        $bill->update($data);

        if ($request->payment_amount > 0) {
            $bill->make_payment($request);
            $bill->adjust_payment($request->payment_amount);
        }

        return Redirect::route('ipdBillings.index')->with('message', 'Ipd Billing Created Successfully.');
    }

    /**
     * Show the form for editing the bill
     */
    public function edit($id)
    {
        $data = IpdBilling::find($id);
        $data['items'] = $data->bill_items();

        $admissions = PatientAdmission::with('patient')->latest()->get();

        $admissions = $admissions->map(function ($item, $key) {
            $item['text'] = $item->patient->name.' / '.$item->patient->mobile.' / id-'.$item->patient->id;
            return $item;
        });
        $data['admissions'] = $admissions;
        return Inertia::render('billing/ipdBilling/edit', ['data' => $data]);
    }

    /**
     * Refund amount from completed bill
     */
    public function add_refund($id)
    {
        $data = IpdBilling::find($id);
        if ($data->status != 'COMPLETE'){
            return Redirect::route('ipdBillings.index')->with('message', 'Bill is not completed yet!');
        }
        $data['items'] = $data->bill_items();

        $admissions = PatientAdmission::with('patient')->latest()->get();

        $admissions = $admissions->map(function ($item, $key) {
            $item['text'] = $item->patient->name.' / '.$item->patient->mobile.' / id-'.$item->patient->id;
            return $item;
        });
        $data['admissions'] = $admissions;
        return Inertia::render('billing/ipdBilling/refund', ['data' => $data]);
    }

    /**
     * Refund amount from completed bill
     */
    public function approve_refund($id)
    {
        $data = IpdBilling::find($id);
        if ($data->status != 'COMPLETE'){
            return Redirect::route('ipdBillings.index')->with('message', 'Bill is not completed yet!');
        }
        $data['items'] = $data->bill_items();

        $admissions = PatientAdmission::with('patient')->latest()->get();

        $admissions = $admissions->map(function ($item, $key) {
            $item['text'] = $item->patient->name.' / '.$item->patient->mobile.' / id-'.$item->patient->id;
            return $item;
        });
        $data['admissions'] = $admissions;
        return Inertia::render('billing/ipdBilling/approve_refund', ['data' => $data]);
    }

    /**
     * Approve refund
     */
    public function approve(Request $request)
    {
        $bill = IpdBilling::where('id', $request->id)->first();

        $data['refund_status']  = 1;
        $data['refund_date']  = date('Y-m-d H:i:s');

        $bill->update($data);

        return Redirect::route('pending_refunds')->with('message', 'Refund Approved Successfully');
    }

    /**
     * Bill listing
     */
    public function pending_refunds(Request $request)
    {

        $ipd = IpdBilling::with('patient_admission.patient')
            ->where('status', '!=', 'INITIATE')
            ->where('refund_amount', '>', 0)
            ->where('refund_status', 0)
            ->latest()
            ->get();

        $opd = OpdBilling::with('patient', 'doctor')
            ->where('refund_amount', '>', 0)
            ->where('refund_status', 0)
            ->where('status', '!=', 'INITIATE')
            ->latest()
            ->get();


        $data['ipd'] = $ipd;
        $data['opd'] = $opd;

        return Inertia::render('billing/ipdBilling/pending_refunds', ['data' => $data]);
    }

    /**
     * Update the opd bill
     */
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'patient_admission_id' => ['required'],
            'payment_status' => ['required'],
            'status' => ['required'],
        ])->validate();

        $data = $request->only(['patient_admission_id', 'payment_status', 'status', 'refund_amount', 'refund_remarks', 'remarks']);

        $bill = IpdBilling::where('id', $request->id)->first();

        $items = $bill->bill_details;

        $data['discount_type'] = $request->discount_type;
        $data['discount_value'] = $request->discount_value;

        $special_discount = 0;
        if ($request->discount_type === "Percentage"){
            $special_discount = ($items->sum('total_tarrif') * $request->discount_value) / 100;
        }else{
            $special_discount = $request->discount_value;
        }

        $discount = $items->sum('discount') + $special_discount;

        $data['total_bill_amount'] = $items->sum('total_tarrif') - $special_discount;

        $data['total_discount_amount']  = $discount;

        $data['total_due_amount']  = $data['total_bill_amount'] - $bill->total_paid_amount;

        if($request->refund_amount > 0){
            $data['refund_by']  = Auth::user()->id;
        }

        $bill->update($data);

        return Redirect::route('ipdBillings.index')->with('message', 'Ipd Billing Updated Successfully.');
    }

    /**
     * Remove bill
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $bill = IpdBilling::find($request->input('id'));
            $bill->delete();
            return redirect()->back();
        }
    }


    // get pending items by a patient
    public function billItems(Request $request, $patient_admission_id){

        $bill_id = $request->query('bill_id');
        if ($bill_id && $bill_id != 0){
            $bill = IpdBilling::find($bill_id);
        }else{
            $bill = IpdBilling::where('patient_admission_id', $patient_admission_id)->first();
            if (!$bill){
                $bill = IpdBilling::create_or_get_initial_bill($request);
            }
        }

        $data['bill'] = [];
        $data['items'] = [];

        if ($bill){
            $data['bill'] = $bill;
            $data['items'] = $bill->bill_items();
        }

        return response()->json($data, Response::HTTP_OK);
    }

    // add item to billing
    public function addItem(Request $request){

        $validator =Validator::make($request->all(), [
            'patient_admission_id' => ['required'],
            'item_type' => ['required'],
            'item_id' => ['required'],
            'item_qty' => ['required'],
            'unit_tarrif' => ['required']
        ])->validate();

        if ($request->bill_id && $request->bill_id != 0){
            $bill = IpdBilling::find($request->bill_id);
        }else{
            $bill = IpdBilling::where('patient_admission_id', $request->patient_admission_id)->first();
            if (!$bill){
                $bill = IpdBilling::create_or_get_initial_bill($request);
                }
        }

        $bill_item_params = $request->except('patient_admission_id');

        $bill_item = $bill->bill_details->where('item_id', $request->item_id)
            ->where('item_type', $request->item_type)->first();

        // hourly bill calculation
        if ($request->start_time && $request->end_time){
            $qty = $bill->calculate_hourly_bill($request);
            $bill_item_params['item_qty'] = $qty;
        }

        // manage pharmacy item
        if ($request->is_for_pharmacy){
            $bill_item_params['item_qty'] = 1;
            $bill_item_params['remarks'] = $request->pharmacy_invoice_number;
            $bill_item_params['item_name'] = $request->item_name.'(#'. $request->pharmacy_invoice_number. ')';
            $bill_item_params['unit_tarrif'] = $request->pharmacy_bill_amount;
        }

        // check patient due limit
//        $limit = $bill->check_due_limit($bill_item_params);
//        if (!$limit){
//            $data['errors']['msg'] = 'Patient due limit over! Please pay previous due first.';
//            return response()->json($data, 400);
//        }

        // check if pharmacy item already exist
        if ($bill_item && $request->is_for_pharmacy){
            $data['message'] = 'Pharmacy item already exist!';
            return response()->json($data, Response::HTTP_OK);
        }
        if($bill_item){
            $bill_item_params['item_qty'] = $bill_item_params['item_qty'] + $bill_item->item_qty;
            $bill_item_params['total_tarrif'] = ($bill_item_params['unit_tarrif'] * $bill_item_params['item_qty']) - $request->discount;
            $bill_item->update($bill_item_params);
        }else{
            $bill_item_params['total_tarrif'] = ($bill_item_params['unit_tarrif'] * $bill_item_params['item_qty']) - $request->discount;
            $bill_item = $bill->bill_details()->create($bill_item_params);
        }

        // check and apply corporate client discount if patient has any
        $bill_item->processCorporateClientDiscount($request);

        // update bill summery
        $bill->update_bill_summery();

        // update bill referral summery
        $bill->update_referral_summery();

        $data['message'] = 'Item Added Successfully.';
        return response()->json($data, Response::HTTP_OK);
    }

    // update item to billing details
    public function updateItem(Request $request){

        Validator::make($request->all(), [
            'patient_admission_id' => ['required'],
            'item_type' => ['required'],
            'item_id' => ['required'],
            'item_qty' => ['required'],
            'unit_tarrif' => ['required']
        ])->validate();


        if ($request->bill_id  && $request->bill_id != 0){
            $bill = IpdBilling::find($request->bill_id);
        }else{
            $bill = IpdBilling::where('patient_admission_id', $request->patient_admission_id)->first();
            if (!$bill){
                $bill = IpdBilling::create_or_get_initial_bill($request);
            }
        }

        $bill_item_params = $request->except('patient_admission_id');

        // hourly bill calculation
        if ($request->start_time && $request->end_time){
            $hourdiff = (strtotime($request->end_time) - strtotime($request->start_time)) / 60;
            $bill_item_params['item_qty'] = $hourdiff;
        }

        // check patient due limit
//        $limit = $bill->check_due_limit($bill_item_params);
//        if (!$limit){
//            $data['errors']['msg'] = 'Patient due limit over! Please pay previous due first.';
//            return response()->json($data, 400);
//        }

        $bill_item_params['total_tarrif'] = ($request->unit_tarrif * $request->item_qty) - $request->discount;

        $bill_item = $bill->bill_details->where('item_id', $request->item_id)
            ->where('item_type', $request->item_type)->first();

        if($bill_item){
            $bill_item->update($bill_item_params);
        }else{
            $bill_item = $bill->bill_details()->create($bill_item_params);
        }

        // check and apply corporate client discount if patient has any
        $bill_item->processCorporateClientDiscount($request);

        // update bill summery
        $bill->update_bill_summery();

        // update bill referral summery
        $bill->update_referral_summery();

        $data['message'] = 'Item Updated Successfully.';
        return response()->json($data, Response::HTTP_OK);
    }

    // remove item from billing while editing
    public function removeItem($item_id){
        $item = IpdBillDetail::find($item_id);
        $bill = IpdBilling::where('id', $item->ipd_billing_id)->first();
        $item->delete();
        // update bill summery
        $bill->update_bill_summery();

        // update bill referral summery
        $bill->update_referral_summery();

        return redirect()->back();
    }

    // get list of service list based on item types
    public function getServices($item_type){

        if($item_type == 'PACKAGE'){
            $items = IpdServicePackage::where('status', 'ACTIVE')->get();
        }elseif($item_type == 'LABTEST'){
            $items = Pathology::where('status', 'ACTIVE')->get();
        }elseif($item_type == 'MEDICINE'){
            $items = IpdService::where('is_for_pharmacy', 1)->get();
        }else{
            $items = IpdService::where('status', 'ACTIVE')->get();
        }

        $items = $items->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });

        $data['items'] = $items;


        return response()->json($data, Response::HTTP_OK);
    }


    // opd bill recipt/invoice
    public function receipt($bill_id){
        $bill = IpdBilling::with('patient_admission.patient', 'patient_admission.doctor',  'patient_admission.referred_personal', 'user')
            ->where('id', $bill_id)->first();
        $bill['bill_details'] = $bill->bill_items();

        return response()->json($bill, Response::HTTP_OK);
    }
}
