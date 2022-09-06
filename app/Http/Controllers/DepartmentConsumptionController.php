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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DepartmentConsumption;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class DepartmentConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = array();
        $hospitalId = empty($query['hospital_id']) ? (!empty(Auth::user()->hospital_id) ? Auth::user()->hospital_id : '') : $query['hospital_id'];

        if (empty($query['hospital_id']) && empty($query['department_id'])) {
            $where = array_merge(array(['department_consumptions.department_id', Auth::user()->department_id]), $where);
        } else if (!empty($query['department_id'])) {
            $where = array_merge(array(['department_consumptions.department_id', $query['department_id']]), $where);
        }

        $where = $this->getQuery($where, $query, $hospitalId);

        $data['data'] = DepartmentConsumption::with('product', 'room')
            ->where($where)
            ->orderBy('id', 'desc')
            ->get();

        $data['department'] = Department::with('hospital')->find(Auth::user()->department_id);
        $data['hospitals'] = Hospital::select(['name', 'id'])->with('departments')->get();

        $data['products'] = $this->getProducts();
        $data['query'] = $query;

        return Inertia::render('departmentConsumption/index', ['param' => $data]);
    }

    public function getQuery($where, $query, $hospitalId)
    {
        if (!empty($hospitalId)) {
            $where = array_merge(array(['department_consumptions.hospital_id', $hospitalId]), $where);
        }

        if (!empty($query['product_for'])) {
            $where = array_merge(array(['department_consumptions.product_for', $query['product_for']]), $where);
        }

        if (!empty($query['status'])) {
            $where = array_merge(array(['department_consumptions.status', $query['status']]), $where);
        }

        if (!empty($query['product_id'])) {
            $where = array_merge(array(['department_consumptions.product_id', $query['product_id']]), $where);
        }

        if (!empty($query['date_range'][0])) {
            $where = array_merge(array([DB::raw('DATE(department_consumptions.created_at)'), '>=', $query['date_range'][0]]), $where);
        }

        if (!empty($query['date_range'][1])) {
            $where = array_merge(array([DB::raw('DATE(department_consumptions.created_at)'), '<=', $query['date_range'][1]]), $where);
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
        $data['patients'] = $patients;


        $data['products'] = $this->getProducts();
        $data['rooms'] = Room::latest()->get();
        return Inertia::render('departmentConsumption/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        $auth_user = Auth::user();

        Validator::make($request->all(), [
            'product_id' => ['required'],
            'product_qty' => ['required'],
        ])->validate();

        $data = $request->all();

        if (!empty($data['patient_id']) && empty($data['product_for'])) {
            $patient = Patient::find($data['patient_id']);

            if ($patient) {
                $data['product_for'] = $patient->name;
            }
        }
        $data['hospital_id'] = $auth_user->hospital_id;
        $data['department_id'] = $auth_user->department_id;

        $inventory = Inventory::where('hospital_id', $auth_user->hospital_id)
            ->where('department_id', $auth_user->department_id)
            ->where('product_id', $request->product_id)->first();

        if ($inventory) {
            if ($request->product_qty > $inventory->quantity) {
                return Redirect::route('consumptions.create')->withErrors(['product_qty' => 'You do not have enough quantity in your inventory!']);
            }
            $inventory->deductInventory($request->product_qty);
            DepartmentConsumption::create($data);

            return Redirect::route('consumptions.index')->with('message', 'Department Consumption Created Successfully.');
        }
        return Redirect::route('consumptions.create')->with('message', 'No inventory found for the selected product!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit($id)
    {
        $user = Auth::user();
        $product_ids = Inventory::where('department_id', $user->department_id)->where('quantity', '>', 0)->pluck('product_id');

        $product_ids = Inventory::where('department_id', $user->department_id)->where('quantity', '>', 0)->pluck('product_id');

        $patients = Patient::latest()->get();

        $patients = $patients->map(function ($item, $key) {
            $item['text'] = $item->name . ' / ' . $item->mobile . ' / id-' . $item->id;
            return $item;
        });

        $data = DepartmentConsumption::find($id);
        $data['products'] = Product::whereIn('id', $product_ids)->get();
        $data['rooms'] = ConsumptionRoom::all();
        $data['patients'] = $patients;

        return Inertia::render('departmentConsumption/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, DepartmentConsumption $departmentConsumption)
    {
        $auth_user = Auth::user();
        Validator::make($request->all(), [
            'product_id' => ['required'],
            'product_qty' => ['required'],
        ])->validate();

        if ($request->has('id')) {

            $consumption = DepartmentConsumption::find($request->input('id'));

            $inventory = Inventory::where('hospital_id', $auth_user->hospital_id)
                ->where('department_id', $auth_user->department_id)
                ->where('product_id', $request->product_id)->first();

            if ($inventory) {
                if ($request->product_qty > $inventory->quantity) {
                    return Redirect::route('consumptions.index')->withErrors(['product_qty' => 'You do not have enough quantity in your inventory!']);
                }
                // find qty difference between two consumption
                $quantity_difference =  $request->product_qty - $consumption->product_qty;
                $inventory->deductInventory($quantity_difference);

                $data = $request->except('patients');

                if (!empty($data['patient_id']) && empty($data['product_for'])) {
                    $patient = Patient::find($data['patient_id']);

                    if ($patient) {
                        $data['product_for'] = $patient->name;
                    }

                    unset($data['patient_id']);
                }

                $consumption->update($data);
                return redirect()->back()
                    ->with('message', 'Department Consumption Updated Successfully.');
            }
        }

        return redirect()->back()
            ->with('message', 'Department Consumption Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Request $request)
    {
        $auth_user = Auth::user();
        if ($request->has('id')) {
            $consumption = DepartmentConsumption::find($request->input('id'));

            $inventory = Inventory::where('hospital_id', $auth_user->hospital_id)
                ->where('department_id', $auth_user->department_id)
                ->where('product_id', $consumption->product_id)->first();

            $inventory->deductInventory(-$consumption->product_qty);

            $consumption->delete();

            return redirect()->back();
        }
    }
}
