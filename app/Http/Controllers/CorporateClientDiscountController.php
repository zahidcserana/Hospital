<?php

namespace App\Http\Controllers;

use App\Models\IpdService;
use App\Models\IpdServicePackage;
use App\Models\OpdService;
use App\Models\OpdServicePackage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\CorporateClient;
use App\Models\CorporateClientDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorporateClientDiscountController extends Controller
{
    /**
     * list of corporate client discount
     */
    public function index($corporate_client_id)
    {
        $discounts = CorporateClientDiscount::with('hospital')
            ->where('corporate_client_id', $corporate_client_id)->get();

        $discounts = collect($discounts)->map(function ($item) {
             $data = $item;
             $data['service'] = $data->service;
             $data['service_package'] = $data->service_package;
             return $data;
        });

        $data['discounts'] = $discounts->all();

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * corporate client create form
     */
    public function create($corporate_client_id)
    {

    }

    /**
     * store new client
     */
    public function store($corporate_client_id, Request $request)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'discount_category' => ['required'],
            'status' => ['required'],
        ])->validate();

        CorporateClientDiscount::create(['corporate_client_id' => $corporate_client_id] + $request->all());

        $data['message'] = 'Discount Created Successfully.';
        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * show single client
     */
    public function show($corporate_client_id, CorporateClientDiscount $corporateClientDiscount)
    {
        //
    }

    /**
     * edit client
     */
    public function edit($corporate_client_id, CorporateClientDiscount $corporateClientDiscount)
    {
        $data = CorporateClientDiscount::find($corporateClientDiscount->id);
        return Inertia::render('settings/common/corporateClientDiscount/edit', ['data' => $data]);
    }

    /**
     * Update the client
     */
    public function update($corporate_client_id, Request $request, CorporateClientDiscount $corporateClientDiscount)
    {
        Validator::make($request->all(), [
            'hospital_id' => ['required'],
            'discount_category' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            CorporateClientDiscount::find($request->input('id'))->update($request->all());

            $data['message'] = 'Discount Updated Successfully.';
            return response()->json($data, Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified client
     */
    public function destroy($corporate_client_id, Request $request)
    {
        if ($request->has('id')) {
            CorporateClientDiscount::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    /**
     * get service and package list while creating
     */
    public function service_packages_list($category)
    {
        if($category == 'OPDSERVICES'){
            $services = OpdService::all();
            $packages = OpdServicePackage::all();
        }else{
            $services = IpdService::all();
            $packages = IpdServicePackage::all();
        }
        $data['packages'] = $packages;
        $data['services'] = $services;

        return response()->json($data, Response::HTTP_OK);
    }
}
