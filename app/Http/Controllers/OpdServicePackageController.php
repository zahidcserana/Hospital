<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\OpdService;
use Illuminate\Http\Request;
use App\Models\OpdServicePackage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class OpdServicePackageController extends Controller
{
    /**
     * OpdServicePackage list
     *
     */
    public function index()
    {
        $data = OpdServicePackage::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/opdServicePackage/index', ['param' => $param]);
    }

    /**
     * Create OpdServicePackage
     */
    public function create()
    {
        $param['services'] = OpdService::all();

        return Inertia::render('settings/common/opdServicePackage/create', ['param' => $param]);
    }

    /**
     * Store OpdServicePackage
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        $data = $request->all();

        $data['code'] = mt_rand(100000, 999999);

        OpdServicePackage::create($data);

        return Redirect::route('opdServicePackages.index')->with('message', 'OpdServicePackage Created Successfully.');
    }


    /**
     * Edit OpdServicePackage
     *
     * @param $id
     */
    public function edit(OpdServicePackage $opdServicePackage)
    {
        $data = OpdServicePackage::find($opdServicePackage->id);

        $param['data'] =  $data;
        $param['services'] = OpdService::all();

        return Inertia::render('settings/common/opdServicePackage/edit', ['param' => $param]);
    }

    /**
     * Update OpdServicePackage
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, OpdServicePackage $opdServicePackage)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            OpdServicePackage::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'OpdServicePackage Updated Successfully.');
        }
    }

    /**
     * Destroy OpdServicePackage
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            OpdServicePackage::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
