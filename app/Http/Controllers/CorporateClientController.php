<?php

namespace App\Http\Controllers;

use App\Models\CorporateClientDiscount;
use App\Models\IpdBilling;
use App\Models\Patient;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\CorporateClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CorporateClientController extends Controller
{
    /**
     * list of corporate client
     */
    public function index()
    {
        $data = CorporateClient::all();

        return Inertia::render('settings/common/corporateClient/index', ['data' => $data]);
    }

    /**
     * corporate client create form
     */
    public function create()
    {
        return Inertia::render('settings/common/corporateClient/create');
    }

    /**
     * store new client
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'company_name' => ['required'],
            'status' => ['required'],
        ])->validate();

        CorporateClient::create($request->all());

        return Redirect::route('corporateClients.index')->with('message', 'Corporate Client Created Successfully.');
    }

    /**
     * show single client
     */
    public function show(CorporateClient $corporateClient)
    {
        //
    }

    /**
     * edit client
     */
    public function edit(CorporateClient $corporateClient)
    {
        $data = CorporateClient::find($corporateClient->id);
        return Inertia::render('settings/common/corporateClient/edit', ['data' => $data]);
    }

    /**
     * Update the client
     */
    public function update(Request $request, CorporateClient $corporateClient)
    {
        Validator::make($request->all(), [
            'company_name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            CorporateClient::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Corporate Client Updated Successfully.');
        }
    }

    /**
     * Remove the specified client
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            CorporateClient::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }

    /**
     * Check billing item discount for corporate client
     *
     * @param $id
     */
    public function checkDiscount($client_id, $bill_type, $item_type, $item_id)
    {
        $client = CorporateClient::find($client_id);
        $discounts = $client->discounts;
        $discount = '';

        if($bill_type == 'OPD') {
            $discounts = $discounts->where('discount_category', 'OPDSERVICES');
        }else{
            $discounts = $discounts->where('discount_category', 'IPDSERVICES');
        }

        if($item_type == 'SERVICE') {
            $discount = $discounts->where('service_id', $item_id)->first();
        }
        if ($item_type == 'PACKAGE'){
            $discount = $discounts->where('service_package_id', $item_id)->first();
        }

        return response()->json($discount, Response::HTTP_OK);

    }
}
