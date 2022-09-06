<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\OpdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class OpdServiceController extends Controller
{
    /**
     * OpdService list
     *
     */
    public function index()
    {
        $data = OpdService::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/opdService/index', $param);
    }

    /**
     * Create OpdService
     */
    public function create()
    {
        return Inertia::render('settings/common/opdService/create');
    }

    /**
     * Store OpdService
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

        OpdService::create($data);

        return Redirect::route('opdServices.index')->with('message', 'OpdService Created Successfully.');
    }


    /**
     * Edit OpdService
     *
     * @param $id
     */
    public function edit(OpdService $opdService)
    {
        $data = OpdService::find($opdService->id);

        $param['data'] =  $data;

        return Inertia::render('settings/common/opdService/edit', $param);
    }

    /**
     * Update OpdService
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, OpdService $opdService)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            OpdService::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'OpdService Updated Successfully.');
        }
    }

    /**
     * Destroy OpdService
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            OpdService::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
