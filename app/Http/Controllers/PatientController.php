<?php

namespace App\Http\Controllers;

use App\Models\CorporateClient;
use App\Models\Hospital;
use App\Models\IpdBilling;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Response;
use Inertia\Inertia;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    /**
     * Patient list
     *
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $term = Arr::get($query, 'term', null);
        $term = $term ? '%' . $term . '%' : null;

        $where = array();
        $where = $this->getQuery($where, $query);

        $data = Patient::where($where)
            ->when($term, function ($query, $term) {
                return  $query->where('name', 'like', $term)
                    ->orWhere('id', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('code', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('mobile', 'like', $term);
            });


        if (isset($query['from_date'])) {
            $data = $data->whereDate('created_at', '>=', $query['from_date']);
        }

        if (isset($query['to_date'])) {
            $data = $data->whereDate('created_at', '<=', $query['to_date']);
        }

        $data = $data->latest()->paginate(20);

        $param['query'] = $query;
        $param['data'] = $data;

        return Inertia::render('patient/index', ['data' => $param]);
    }

    // search query builder
    public function getQuery($where, $query)
    {
        if (!empty($query['search_type']) && !empty($query['search_text'])) {

            if ($query['search_type'] == 'patient_name') {
                $where = array_merge(array(['patients.name', 'LIKE', '%' . $query['search_text'] . '%']), $where);
            }

            if ($query['search_type'] == 'patient_code') {
                $where = array_merge(array(['patients.code',   $query['search_text']]), $where);
            }

            if ($query['search_type'] == 'patient_id') {
                $where = array_merge(array(['patients.id',   $query['search_text']]), $where);
            }

            if ($query['search_type'] == 'mobile') {
                $where = array_merge(array(['patients.mobile',   $query['search_text']]), $where);
            }
        }

        return $where;
    }

    /**
     * Create Patient
     */
    public function create()
    {
        $data['code'] = mt_rand(10000000, 99999999);
        $data['clients'] = CorporateClient::all();
        return Inertia::render('patient/create', ['data' => $data]);
    }

    /**
     * Store Patient
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'mobile' => ['required'],
            'gender' => ['required'],
            'contact_person_mobile' => ['required'],
        ])->validate();


        $file_path = '';
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }

        $data = $request->all();

        $data['photo'] = $file_path;

        if ($request->dob) {
            $data['age'] = abs(strtotime(Date('yy-m-d')) - strtotime($request->dob))  / (365 * 60 * 60 * 24);
        } elseif ($request->age) {
            $data['dob'] = date('Y-m-d', strtotime(-$request->age . ' year'));
        }

        $data['uhid'] = mt_rand(100000, 999999);

        $patient = Patient::create($data);
        // generate and set patient code
        $patient->generate_code();

        return Redirect::route('patients.index')->with('message', 'Patient Created Successfully.');
    }


    /**
     * Edit Patient
     *
     * @param $id
     */
    public function edit(Patient $patient)
    {
        $data = Patient::find($patient->id);
        $data['clients'] = CorporateClient::all();
        $param['data'] =  $data;

        return Inertia::render('patient/edit', $param);
    }

    /**
     * Update Patient
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, Patient $patient)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'mobile' => ['required'],
            'address' => ['required'],
            'gender' => ['required'],
            'dob' => ['required'],
            'contact_person_name' => ['required'],
            'contact_person_relation' => ['required'],
            'contact_person_mobile' => ['required'],
            'status' => ['required'],
        ])->validate();


        $file_path = '';
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }

        $data = $request->all();
        if ($file_path) {
            $data['photo'] = $file_path;
        }

        if ($request->has('id')) {
            Patient::find($request->input('id'))->update($data);
            return redirect()->back()
                ->with('message', 'Patient Updated Successfully.');
        }
    }

    /**
     * Destroy Patient
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $patient = Patient::find($request->input('id'));
            if ($patient->opd_bills->count() > 0 or $patient->ipd_admissions->count() > 0) {
                return redirect()->back()
                    ->with('message', "Patient can't be delete!");
            }else{
                Patient::find($request->input('id'))->delete();
                return redirect()->back();
            }
        }
    }

    /**
     * Find & return hospital departments
     *
     * @param $id
     */
    public function unpaid_bills($patient_id, $bill_type)
    {
        $patient = Patient::find($patient_id);

        if ($bill_type == 'OPD') {
            $opd_bills = $patient->opd_bills->where('total_due_amount', '>', 0);

            $bills = [];
            foreach ($opd_bills as $bill){
                array_push($bills, $bill);
            }
        } else {
            $bills = IpdBilling::whereHas('patient_admission', function ($query) use ($patient_id) {
                $query->where('patient_id',  '=', $patient_id)
                    ->where('total_due_amount',  '>', 0);
            })->get();
        }

        $data['bills'] = $bills;

        return response()->json($data, Response::HTTP_OK);
    }
}
