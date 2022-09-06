<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Pathology;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PathologyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Pathology::with('hospital', 'department')->get();

        return Inertia::render('settings/common/pathology/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Pathology';

        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::all();
        $data['sample_types'] = ["BLOOD", "URINE", "STOOL", "OTHER"];

        return Inertia::render('settings/common/pathology/create', ['data' => $data]);
    }

    /**
     * Pathology a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'department_id' => ['required'],
            'name' => ['required'],
            'sample_type' => ['required'],
            'tariff' => ['required'],
            'status' => ['required'],
        ])->validate();

        # generate six digit hospital code and update request array
        $code = mt_rand(100000, 999999);

        Pathology::create(['code' => $code] + $request->all());

        return Redirect::route('pathologies.index')->with('message', 'Pathology Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pathology  $pathology
     * @return \Illuminate\Http\Response
     */
    public function show(Pathology $pathology)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pathology  $pathology
     * @return \Illuminate\Http\Response
     */
    public function edit(Pathology $pathology)
    {
        $data = Pathology::find($pathology->id);
        $data['title'] = 'Update Pathology';

        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::all();
        $data['sample_types'] = ["BLOOD", "URINE", "STOOL", "OTHER"];

        return Inertia::render('settings/common/pathology/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pathology  $pathology
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pathology $pathology)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'department_id' => ['required'],
            'name' => ['required'],
            'sample_type' => ['required'],
            'tariff' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Pathology::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Pathology Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pathology  $Pathology
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Pathology::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
