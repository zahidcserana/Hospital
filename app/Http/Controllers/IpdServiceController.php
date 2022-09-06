<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\IpdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class IpdServiceController extends Controller
{
    /**
     * IpdService list
     *
     */
    public function index()
    {
        $data = IpdService::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/ipdService/index', $param);
    }

    /**
     * Create IpdService
     */
    public function create()
    {
        return Inertia::render('settings/common/ipdService/create');
    }

    /**
     * Store IpdService
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

        IpdService::create($data);

        return Redirect::route('ipdServices.index')->with('message', 'IpdService Created Successfully.');
    }


    /**
     * Edit IpdService
     *
     * @param $id
     */
    public function edit(IpdService $ipdService)
    {
        $data = IpdService::find($ipdService->id);

        $param['data'] =  $data;

        return Inertia::render('settings/common/ipdService/edit', $param);
    }

    /**
     * Update IpdService
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, IpdService $ipdService)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            IpdService::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'IpdService Updated Successfully.');
        }
    }

    /**
     * Destroy IpdService
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            IpdService::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
