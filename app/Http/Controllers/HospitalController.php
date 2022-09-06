<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Response;
use Inertia\Inertia;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Hospital list
     *
     */
    public function index()
    {
        $data = Hospital::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/hospital/index', ['param' => $param]);
    }
        /**
     * Hospital list
     *
     */
    public function dashboard()
    {
        $data['doctor'] = Doctor::all()->count();


        $param['data'] = $data;

        return Inertia::render('home', ['param' => $param]);
    }


    /**
     * Hospital list json
     *
     */
    public function list()
    {
        $data = Hospital::all();
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Create Hospital
     */
    public function create()
    {
        return Inertia::render('settings/common/hospital/create');
    }

    /**
     * Store Hospital
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'district' => ['required'],
        ])->validate();

        $data = $request->all();

        $data['code'] = mt_rand(100000, 999999);

        Hospital::create($data);

        return Redirect::route('hospitals.index')->with('message', 'Hospital Created Successfully.');
    }


    /**
     * Edit Hospital
     *
     * @param $id
     */
    public function edit(Hospital $hospital)
    {
        $data = Hospital::find($hospital->id);

        $param['data'] =  $data;

        return Inertia::render('settings/common/hospital/edit', ['param' => $param]);
    }

    /**
     * Update Hospital
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, Hospital $hospital)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Hospital::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Hospital Updated Successfully.');
        }
    }

    /**
     * Destroy Hospital
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Hospital::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }

    /**
     * Find & return hospital departments
     *
     * @param $id
     */
    public function departments($hospital_id)
    {
        $hospital = Hospital::find($hospital_id);

        $data['departments'] = $hospital->departments;

        return response()->json($data, Response::HTTP_OK);

    }


}
