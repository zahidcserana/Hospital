<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Doctor;
use App\Models\IpdServicePackage;
use App\Models\Patient;
use App\Models\PaymentHistory;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\PatientAdmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PatientAdmissionController extends Controller
{
    /**
     * PatientAdmission list
     *
     */
    public function index(Request $request)
    {
        $query = $request->query();

        $data = PatientAdmission::with('patient');

        if (isset($query['from_date']) && isset($query['to_date'])) {
            $dateS = new Carbon($request->from_date);
            $dateE = new Carbon($request->to_date);
            $data =  $data->whereBetween('created_at', [$dateS->format('Y-m-d')." 00:00:00", $dateE->format('Y-m-d')." 23:59:59"]);
        }

        if (isset($query['search_type']) && $query['search_type'] == 'patient_code'){
            $data = $data->where('bill_no',  $query['search_text']);
        }

        $data = $data->whereHas('patient', function ($query) use ($request) {
            $params = $request->query();
            if (!empty($params['search_type']) &&  !empty($params['search_text'])) {
                 if ($params['search_type'] == 'patient_id') {
                    $query = $query->where('patients.id', '=', $params['search_text']);
                } elseif ($params['search_type'] == 'mobile') {
                    $query = $query->where('patients.mobile', '=', $params['search_text']);
                } elseif ($params['search_type'] == 'patient_name') {
                    $query = $query->where('patients.name', 'LIKE', '%' . $params['search_text'] . '%');
                }
            }
            return $query;
        });


        $data = $data->latest()->paginate(20);


        if (isset($query['search_type']) && $query['search_type'] == 'staying_days'){
            $data = collect($data)->filter(function ($item, $key) use ($query) {
                if ($item->release_date && $item->admission_date){
                    $days = (int)((strtotime($item->release_date) - strtotime($item->admission_date))/86400);
                    if ($days == $query['search_text']){
                        return true;
                    }
                    return false;
                }
                return false;
            });
         }


        $param['query'] = $query;
        $param['data'] = $data;

        return Inertia::render('patientAdmission/index', ['param' => $param]);
    }

    /**
     * Create PatientAdmission
     */
    public function create()
    {
        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name.' / '.$item->mobile.' / id-'.$item->id;
            return $item;
        });
        $param['patients'] = $patients;

        $doctors = Doctor::latest()->get();

        $doctors = $doctors->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });
        $param['doctors'] = $doctors;

        $param['beds'] = Bed::latest()->get();
        $param['rooms'] = Room::latest()->get();
        $param['packages'] = IpdServicePackage::all();

        return Inertia::render('patientAdmission/create', ['param' => $param]);
    }

    /**
     * Store PatientAdmission
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'patient_id' => ['required'],
            'doctor_id' => ['required'],
            'referred_by' => ['required'],
            'room' => ['required'],
            'admission_date' => ['required'],
            'under_package' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->under_package == 'Yes'){
            Validator::make($request->all(), [
                'package_id' => ['required'],
                'advance_payment_amount' => ['required'],
                'payment_type' => ['required'],
            ])->validate();

            $package = IpdServicePackage::find($request->package_id);

//            if ($request->advance_payment_amount < $package->tariff ){
//
//                Validator::make($request->all(), [
//                    'payment_amount' => ['required'],
//                ])->validate();
//            }
        }

        $data = $request->all();

        $data['bill_no'] = mt_rand(100000, 999999);
        $data['hospital_id'] = Auth::user()->hospital_id;
        $data['created_by'] = Auth::user()->id;

        $admission = PatientAdmission::create($data);

        if ($request->under_package == 'Yes' or $request->advance_payment_amount > 0){
            // if under package create ipd billing for this admission
            $admission->process_admission_with_package($request);
        }



        return Redirect::route('patientAdmissions.index')->with('message', 'Patient Admission Created Successfully.');
    }


    /**
     * Edit PatientAdmission
     *
     * @param $id
     */
    public function edit(PatientAdmission $patientAdmission)
    {
        $data = PatientAdmission::with('bill')->find($patientAdmission->id);

        $param['data'] = $data;

        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name.' / '.$item->mobile.' / id-'.$item->id;
            return $item;
        });
        $param['patients'] = $patients;

        $doctors = Doctor::latest()->get();

        $doctors = $doctors->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });
        $param['doctors'] = $doctors;

//        $param['bill'] = $data->bill;
        $param['beds'] = Bed::latest()->get();
        $param['rooms'] = Room::all();
        $param['packages'] = IpdServicePackage::all();

        return Inertia::render('patientAdmission/edit', ['param' => $param]);
    }

    /**
     * Update PatientAdmission
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, PatientAdmission $patientAdmission)
    {
        Validator::make($request->all(), [
            'patient_id' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $data = $request->all();
            if ($request->release_date){
                $data['status'] = 'Release';
            }
            PatientAdmission::find($request->input('id'))->update($data);
            return redirect()->back()
                ->with('message', 'PatientAdmission Updated Successfully.');
        }
    }

    /**
     * Destroy PatientAdmission
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $admission = PatientAdmission::find($request->input('id'));
            if ($admission->bill) {
                return redirect()->back()
                    ->with('message', "Admission can't be delete!");
            }else{
                PatientAdmission::find($request->input('id'))->delete();
                return redirect()->back();
            }
        }
    }


    public function facesheet($id){
        $admission = PatientAdmission::find($id);
        $admission['patient'] = $admission->patient;
        $admission['referred_personal'] = $admission->referred_personal;
        $admission['doctor'] = $admission->doctor;
        $admission['hospital'] = $admission->hospital;
        $admission['user'] = $admission->user;
        $admission['package'] = $admission->package;
        return response()->json($admission, Response::HTTP_OK);
    }
}
