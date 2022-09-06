<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\DoctorsSpeciality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorsSpecialityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DoctorsSpeciality::all();

        return Inertia::render('settings/common/doctorsSpeciality/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Doctors Speciality';

        return Inertia::render('settings/common/doctorsSpeciality/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();


        DoctorsSpeciality::create($request->all());

        return Redirect::route('doctor-specialities.index')->with('message', 'Doctors Speciality Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DoctorsSpeciality  $doctorsSpeciality
     * @return \Illuminate\Http\Response
     */
    public function show(DoctorsSpeciality $doctorsSpeciality)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DoctorsSpeciality  $doctorSpeciality
     * @return \Illuminate\Http\Response
     */
    public function edit(DoctorsSpeciality $doctorSpeciality)
    {
        $data = DoctorsSpeciality::find($doctorSpeciality->id);
        $data['title'] = 'Update Doctors Speciality';

        return Inertia::render('settings/common/doctorsSpeciality/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DoctorsSpeciality  $doctorSpeciality
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DoctorsSpeciality $doctorSpeciality)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            DoctorsSpeciality::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Doctors Speciality Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DoctorsSpeciality  $doctorSpeciality
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            DoctorsSpeciality::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
