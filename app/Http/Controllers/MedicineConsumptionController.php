<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Hospital;
use App\Models\Inventory;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ConsumptionRoom;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicineConsumption;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class MedicineConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = array();
        $hospitalId = empty($query['hospital_id']) ? (!empty(Auth::user()->hospital_id) ? Auth::user()->hospital_id : '') : $query['hospital_id'];

        $where = $this->getQuery($where, $query, $hospitalId);

        $results = MedicineConsumption::with('medicine')
            ->where($where)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($results as $row) {
            $row->product_for = Patient::find($row->patient_id)->name;
        }

        if (!empty($query['medicine_id'])) {
            $query['medicine'] = Medicine::find($query['medicine_id']);
        }

        $data['data'] = $results;
        $data['hospitals'] = Hospital::select(['name', 'id'])->get();
        $data['products'] = $this->getMedicine();
        $data['query'] = $query;

        return Inertia::render('medicineConsumption/index', ['param' => $data]);
    }

    public function getQuery($where, $query, $hospitalId)
    {
        if (!empty($hospitalId)) {
            $where = array_merge(array(['medicine_consumptions.hospital_id', $hospitalId]), $where);
        }

        if (!empty($query['medicine_id'])) {
            $where = array_merge(array(['medicine_consumptions.medicine_id', $query['medicine_id']]), $where);
        }

        if (!empty($query['date_range'][0])) {
            $where = array_merge(array([DB::raw('DATE(medicine_consumptions.date)'), '>=', $query['date_range'][0]]), $where);
        }

        if (!empty($query['date_range'][1])) {
            $where = array_merge(array([DB::raw('DATE(medicine_consumptions.date)'), '<=', $query['date_range'][1]]), $where);
        }

        return $where;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name . ' / ' . $item->mobile . ' / id-' . $item->id;
            return $item;
        });

        $hospitalId = (!empty(Auth::user()->hospital_id) ? Auth::user()->hospital_id : null);

        $where = [];
        $where = array_merge(array([DB::raw('DATE(medicine_consumptions.date)'),  date('Y-m-d')]), $where);

        if (!empty($hospitalId)) {
            $where = array_merge(array(['medicine_consumptions.hospital_id', $hospitalId]), $where);
        }


        $results = MedicineConsumption::with('medicine')
            ->where($where)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($results as $row) {
            $row->product_for = Patient::find($row->patient_id)->name;
        }

        $data['data'] = $results;
        $data['patients'] = $patients;
        $data['products'] = $this->getMedicine();

        return Inertia::render('medicineConsumption/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        $auth_user = Auth::user();

        Validator::make($request->all(), [
            'medicine_id' => ['required'],
            'patient_id' => ['required'],
            'quantity' => ['required'],
        ])->validate();

        $data = $request->all();

        $todaysMedicineConsumption = MedicineConsumption::where('date', date('Y-m-d'))->first();
        $serial = 1;

        if ($todaysMedicineConsumption) {
            if ($todaysMedicineConsumption->where('medicine_id', $data['medicine_id'])->first()) {
                return Redirect::back()->with('message', 'Already exists!');
            }

            $serial = $todaysMedicineConsumption->serial;
        } else {
            $lastMedicineConsumption = MedicineConsumption::orderByDesc('serial')->first();

            if ($lastMedicineConsumption) {
                $serial = $lastMedicineConsumption->serial + 1;
            }
        }

        $medicineConsumption = new MedicineConsumption();
        $data['date'] = date('Y-m-d');
        $data['serial'] = $serial;
        $data['hospital_id'] = $auth_user->hospital_id;
        $medicineConsumption->create($data);

        return Redirect::back()->with('message', 'Medicine Consumption Created Successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit($id)
    {
        return;
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, MedicineConsumption $medicineConsumption)
    {
        return;
    }

    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            MedicineConsumption::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }

    public function searchMedicine(Request $request)
    {
        $term = $request->input('term') ?? '';

        $data = $this->getMedicine($term);

        return response()->json($data);
    }
}
