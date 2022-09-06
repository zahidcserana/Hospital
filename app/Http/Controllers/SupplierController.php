<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Supplier::all();

        return Inertia::render('settings/common/supplier/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Supplier';

        return Inertia::render('settings/common/supplier/create', ['data' => $data]);
    }

    /**
     * Supplier a newly created resource in storage.
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

        # generate six digit hospital code and update request array
        $code = mt_rand(100000, 999999);

        Supplier::create(['code' => $code] + $request->all());

        return Redirect::route('suppliers.index')->with('message', 'Supplier Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $data = Supplier::find($supplier->id);
        $data['title'] = 'Update Supplier';

        return Inertia::render('settings/common/supplier/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Supplier::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Supplier Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Supplier::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
