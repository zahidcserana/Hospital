<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Hospital;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Designation::with('hospital', 'department')->get();

        return Inertia::render('settings/common/designation/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['page_title'] = 'New Designation';

        $data['hospitals'] = Hospital::all();
//        $data['departments'] = Department::all();


        return Inertia::render('settings/common/designation/create', ['data' => $data]);
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
            'title' => ['required'],
            'description' => ['required'],
            'hospital_id' => ['required'],
            'department_id' => ['required'],
        ])->validate();


        Designation::create($request->all());

        return Redirect::route('designations.index')->with('message', 'Designation Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show(Designation $designation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function edit(Designation $designation)
    {
        $data = Designation::find($designation->id);
        $data['page_title'] = 'Update Designation';

        $data['hospitals'] = Hospital::all();
//        $data['departments'] = Department::all();


        return Inertia::render('settings/common/designation/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Designation $designation)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'department_id' => ['required'],
            'title' => ['required']
        ])->validate();

        if ($request->has('id')) {
            Designation::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Designation Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Designation::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
