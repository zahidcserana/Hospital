<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IpdService;
use Illuminate\Http\Request;
use App\Models\IpdServicePackage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class IpdServicePackageController extends Controller
{
    /**
     * IpdServicePackage list
     *
     */
    public function index()
    {
        $data = IpdServicePackage::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/ipdServicePackage/index', ['param' => $param]);
    }

    /**
     * Create IpdServicePackage
     */
    public function create()
    {
        $param['services'] = IpdService::all();

        return Inertia::render('settings/common/ipdServicePackage/create', ['param' => $param]);
    }

    /**
     * Store IpdServicePackage
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

        IpdServicePackage::create($data);

        return Redirect::route('ipdServicePackages.index')->with('message', 'IpdServicePackage Created Successfully.');
    }


    /**
     * Edit IpdServicePackage
     *
     * @param $id
     */
    public function edit(IpdServicePackage $ipdServicePackage)
    {
        $data = IpdServicePackage::find($ipdServicePackage->id);

        $param['data'] =  $data;
        $param['services'] = IpdService::all();

        return Inertia::render('settings/common/ipdServicePackage/edit', ['param' => $param]);
    }

    /**
     * Update IpdServicePackage
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, IpdServicePackage $ipdServicePackage)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            IpdServicePackage::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'IpdServicePackage Updated Successfully.');
        }
    }

    /**
     * Destroy IpdServicePackage
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            IpdServicePackage::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
