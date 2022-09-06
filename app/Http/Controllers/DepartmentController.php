<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Department::with('hospital')->get();

        return Inertia::render('settings/common/department/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Department';

        $data['hospitals'] = Hospital::all();
        $data['users'] = User::all();

        return Inertia::render('settings/common/department/create', ['data' => $data]);
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
            'hospital_id' => ['required'],
            'dept_head_id' => ['required'],
            'dept_admin_id' => ['required'],
        ])->validate();


        Department::create($request->all());

        return Redirect::route('departments.index')->with('message', 'Department Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        $data = Department::find($department->id);
        $data['title'] = 'Update Department';

        $data['hospitals'] = Hospital::all();
        $data['users'] = User::all();

        return Inertia::render('settings/common/department/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'name' => ['required'],
            'dept_head_id' => ['required'],
            'dept_admin_id' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Department::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Department Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Department::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
