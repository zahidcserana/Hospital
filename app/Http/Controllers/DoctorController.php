<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Designation;
use App\Models\Department;
use App\Models\DoctorsSpeciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = array();
        $where = $this->getQuery($where, $query);

        $results = Doctor::with('hospital', 'designation', 'speciality')
            ->where($where);

        if (!empty($query['speciality_id'])){
            $results = $results->whereHas('speciality', function ($query) use ($request) {
                $params = $request->query();
                return $query->where('doctors_specialities.id', '=', $params['speciality_id']);
            });
        }

        $results = $results ->latest()->get();

        $data['specialities'] =  DoctorsSpeciality::all();
        $data['query'] = $query;
        $data['data'] = $results;

        return Inertia::render('users/doctor/index', ['data' => $data]);
    }

    // search query builder
    public function getQuery($where, $query)
    {
        if (!empty($query['search_type']) && !empty($query['search_text'])) {

            if ($query['search_type'] == 'doctor_name') {
                $where = array_merge(array(['doctors.name', 'LIKE', '%' . $query['search_text'] . '%']), $where);
            }

            if ($query['search_type'] == 'doctor_code') {
                $where = array_merge(array(['doctors.code',   $query['search_text'] ]), $where);
            }

            if ($query['search_type'] == 'doctor_id') {
                $where = array_merge(array(['doctors.id',   $query['search_text'] ]), $where);
            }

            if ($query['search_type'] == 'mobile') {
                $where = array_merge(array(['doctors.mobile',   $query['search_text'] ]), $where);
            }

        }

        return $where;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['code'] = mt_rand(10000000, 99999999);
        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::where('is_for_doctor', 1)->get();
        $data['designations'] = Designation::where('is_for_doctor', 1)->get();
        $data['specialities'] = DoctorsSpeciality::all();
        $data['types'] = ['INHOUSE', 'VISITING', 'REFERRED PHYSICIAN', 'REFERRED PERSONAL'];

        return Inertia::render('users/doctor/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'code' => ['required'],
            'type' => ['required'],
            'mobile' => ['required'],
            'designation_id' => ['required'],
            'status' => ['required']
        ])->validate();

        $file_path = '';
        if($request->file('photo')) {
            $file = $request->file('photo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }

        $data = $request->all();
        $data['photo'] = $file_path;

        Doctor::create($data);

        return Redirect::route('doctors.index')->with('message', 'Doctor Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {
        $data = Doctor::find($doctor->id);
        $data['title'] = 'Update Doctor';
        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::where('is_for_doctor', 1)->get();
        $data['designations'] = Designation::where('is_for_doctor', 1)->get();
        $data['specialities'] = DoctorsSpeciality::all();
        $data['types'] = ['INHOUSE', 'VISITING', 'REFERRED PHYSICIAN', 'REFERRED PERSONAL'];

        return Inertia::render('users/doctor/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'type' => ['required'],
            'mobile' => ['required'],
            'designation_id' => ['required'],
            'status' => ['required'],
        ])->validate();

        $file_path = '';
        if($request->file('photo')) {
            $file = $request->file('photo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $request->file('photo')->storeAs('uploads', $fileName, 'public');
            $file_path = '/storage/' . $filePath;
        }
        $data = $request->all();
        if($file_path){
            $data['photo'] = $file_path;
        }

        if ($request->has('id')) {
            Doctor::find($request->input('id'))->update($data);
            return redirect()->back()
                ->with('message', 'Doctor Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Doctor::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
