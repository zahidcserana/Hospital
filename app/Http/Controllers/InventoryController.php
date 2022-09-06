<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Hospital;
use Inertia\Inertia;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Inventory list
     *
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = array();
        $where = array_merge(array(['inventories.type', 1]), $where);

        if (empty($query['hospital_id'])) {
            $where = array_merge(array(['inventories.hospital_id', Auth::user()->hospital_id]), $where);
        } else {
            $where = array_merge(array(['inventories.hospital_id', $query['hospital_id']]), $where);
        }
        if (!empty($query['product_id'])) {
            $where = array_merge(array(['inventories.product_id', $query['product_id']]), $where);
        }

        $data = Inventory::with('product', 'product.product_category')->where($where)->get();

        $param['query'] = $query;
        $param['data'] = $data;
        $param['type'] = 1;
        $param['hospital'] = Hospital::find(Auth::user()->hospital_id);
        $param['hospitals'] = Hospital::select(['name', 'id'])->get();
        $param['products'] = $this->getProducts();

        return Inertia::render('inventory/index', ['param' => $param]);
    }

    /**
     * Dept Inventory list
     *
     */
    public function deptIndex(Request $request)
    {
        $query = $request->query();
        $where = array();
        $where = array_merge(array(['inventories.type', 2]), $where);

        if (empty($query['department_id'])) {
            $where = array_merge(array(['inventories.department_id', Auth::user()->department_id]), $where);
        } else {
            $where = array_merge(array(['inventories.department_id', $query['department_id']]), $where);
        }
        if (!empty($query['product_id'])) {
            $where = array_merge(array(['inventories.product_id', $query['product_id']]), $where);
        }

        $data = Inventory::with('product', 'product.product_category')->where($where)->get();

        $param['query'] = $query;
        $param['data'] = $data;
        $param['type'] = 2;
        $param['department'] = Department::with('hospital')->find(Auth::user()->department_id);
        $param['hospitals'] = Hospital::select(['name', 'id'])->with('departments')->get();
        $param['departments'] = Department::pluck('name', 'id');
        $param['products'] = $this->getProducts();

        return Inertia::render('inventory/index', ['param' => $param]);
    }

    /**
     * Destroy Inventory
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Inventory::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
