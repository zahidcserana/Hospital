<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\IpdService;
use App\Models\IpdServicePackage;
use App\Models\OpdBilling;
use App\Models\OpdService;
use App\Models\OpdServicePackage;
use App\Models\Referral;
use App\Models\ReferralSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ReferralSettingController extends Controller
{
    /**
     * Referral setting list
     *
     */
    public function index()
    {
        $settings = ReferralSetting::with('person')->latest()->get();


        $settings = $settings->map(function ($item, $key) {
            $data = $item;
            $item = $data->item;
            $data['item'] = $item;
            return $data;
        });

        $param['data'] = $settings;

        return Inertia::render('referralSetting/index', ['param' => $param]);
    }

    /**
     * Create Referral setting
     */
    public function create()
    {
        $doctors = Doctor::all();
        $doctors = $doctors->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });
        $data['doctors'] = $doctors;
        $opd_services = OpdService::all();
        $opd_packages = OpdServicePackage::all();
        $ipd_services = IpdService::all();
        $ipd_packages = IpdServicePackage::all();

        $opd_services = $opd_services->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $data['opd_services'] = $opd_services;

        $opd_packages = $opd_packages->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $data['opd_packages'] = $opd_packages;

        $ipd_services = $ipd_services->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $data['ipd_services'] = $ipd_services;

        $ipd_packages = $ipd_packages->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $data['ipd_packages'] = $ipd_packages;

        return Inertia::render('referralSetting/create', ['param' => $data]);
    }

    /**
     * Store Hospital
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'person_id' => ['required'],
        ])->validate();

        $data = $request->all();
        if ($request->service_type == 'OPD' or $request->service_type == 'IPD'){
            foreach ($request->services as $value) {
                ReferralSetting::create(['person_id' => $request->person_id, 'item_type' => $request->item_type, 'service_type' => $request->service_type, 'service_id' => $value['id'], 'percentage_value' => $value['percentage_value']]);
            }
        }else{
            ReferralSetting::create($data);
        }

        return Redirect::route('referralSetting.index')->with('message', 'Referral Setting Created Successfully.');
    }


    /**
     * Edit referralSetting
     *
     * @param $id
     */
    public function edit(ReferralSetting $referralSetting)
    {
        $data = ReferralSetting::find($referralSetting->id);
        $doctors = Doctor::all();
        $doctors = $doctors->map(function ($item, $key) {
            $item['text'] = $item->name;
            return $item;
        });
        $param['doctors'] = $doctors;

        $opd_services = OpdService::all();
        $opd_packages = OpdServicePackage::all();
        $ipd_services = IpdService::all();
        $ipd_packages = IpdServicePackage::all();

        $opd_services = $opd_services->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $param['opd_services'] = $opd_services;

        $opd_packages = $opd_packages->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $param['opd_packages'] = $opd_packages;

        $ipd_services = $ipd_services->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $param['ipd_services'] = $ipd_services;

        $ipd_packages = $ipd_packages->map(function ($item, $key) {
            $item['text'] = $item->name;
            $item['percentage_value'] = 0;
            return $item;
        });
        $param['ipd_packages'] = $ipd_packages;

        $param['data'] = $data;

        return Inertia::render('referralSetting/edit', ['param' => $param]);
    }

    /**
     * Update referralSetting
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, ReferralSetting $referralSetting)
    {
        Validator::make($request->all(), [
            'person_id' => ['required'],
            'percentage_value' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            ReferralSetting::find($request->input('id'))->update($request->only(['person_id', 'percentage_value', 'service_id', 'status']));
            return redirect()->back()
                ->with('message', 'Referral Setting Updated Successfully.');
        }
    }

    /**
     * Destroy ReferralSetting
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            ReferralSetting::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }




}
