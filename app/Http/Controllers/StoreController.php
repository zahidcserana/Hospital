<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Store;
use App\Models\Hospital;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Store::with('hospital', 'department')->get();

        return Inertia::render('settings/common/store/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Store';

        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::all();

        return Inertia::render('settings/common/store/create', ['data' => $data]);
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
            'hospital_id' => ['required'],
            'department_id' => ['required'],
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        # generate six digit hospital code and update request array
        $code = mt_rand(100000, 999999);

        Store::create(['code' => $code] + $request->all());

        return Redirect::route('stores.index')->with('message', 'Store Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        $data = Store::find($store->id);
        $data['title'] = 'Update Store';


        $data['hospitals'] = Hospital::all();
        $data['departments'] = Department::all();


        return Inertia::render('settings/common/store/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'department_id' => ['required'],
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Store::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Store Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Store::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
