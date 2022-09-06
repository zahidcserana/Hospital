<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Image;
use App\Models\Product;
use Carbon\Traits\Date;
use App\Models\Hospital;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\Department;
use App\Models\Requisition;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\RequisitionDetail;
use Illuminate\Support\Facades\DB;
use App\Events\DeliveredRequisition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RequisitionController extends Controller
{
    /**
     * Requisition list
     *
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $where = [];
        $where = array_merge(array(['requisitions.type', 1]), $where);
        $hospitalId = empty($query['hospital_id']) ? (!empty(Auth::user()->hospital_id) ? Auth::user()->hospital_id : '') : $query['hospital_id'];

        $requisitionAccess = [];
        if (!empty(Auth::user()->role->name) && !empty(\config('settings.requisition_access')[Auth::user()->role->name])) {
            $requisitionAccess = \config('settings.requisition_access')[Auth::user()->role->name];
        }

        $where = $this->getQuery($where, $query, $hospitalId);

        $data = Requisition::with('items', 'items.product', 'category', 'hospital', 'user')
            ->where($where)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($data as $row) {
            $row->image = $row->images->last();
            $row->nextStatus = $this->getNextStatus($row);
            $row->statusAccess = $this->getAccess($row, $requisitionAccess);
            $row->editAccess = in_array($row->status, $requisitionAccess) && in_array(Auth::user()->role->name, ['SA', 'HO', 'SAdmin', 'DF']);
        }

        /* Category wise product quantity */
        $catProducts = $this->getCatProduct($data, 1, $hospitalId);
        /* ****  ******  *****  ***** */

        $param['query'] = $query;
        $param['data'] = $data;
        $param['catProducts'] = $catProducts;
        $param['type'] = 1;
        $param['hospital'] = Hospital::find(Auth::user()->department_id);
        $param['hospitals'] = Hospital::select(['name', 'id'])->get();
        $param['status'] = \config('settings.req_status');
        $param['purchaseTypes'] = \config('settings.purchaseTypes');
        $param['categories'] = ProductCategory::pluck('name', 'id');
        $param['canCreate'] = in_array(Auth::user()->role->name, ['SA', 'Admin', 'SAdmin']);

        return Inertia::render('requisition/index', ['param' => $param]);
    }

    private function getAccess($requisition, $requisitionAccess)
    {
        if (Auth::user()->role->name == 'Admin') {
            return true;
        }

        return in_array($requisition->status, $requisitionAccess);
    }

    private function getCatProduct($data, $type = 1, $hospitalId, $departmentId = null)
    {
        $requisitions = $data->whereNotIn('status', ['DELIVERED', 'RECEIVED']);

        $products = RequisitionDetail::whereIn('requisition_id', $requisitions->pluck('id'))
            ->groupBy('product_id')
            ->selectRaw('sum(expected_quantity) as total_expected, product_id')
            ->with('product', 'product.product_category')
            ->get();

        foreach ($products as $i => $row) {
            $where = array();
            $where = array_merge(array(['type', $type]), $where);

            if (!empty($hospitalId)) {
                $where = array_merge(array(['hospital_id', $hospitalId]), $where);
            }
            if (!empty($departmentId)) {
                $where = array_merge(array(['department_id', $departmentId]), $where);
            }

            $where = array_merge(array(['product_id', $row->product_id]), $where);

            $row->stock = Inventory::where($where)->sum('quantity');
        }

        $catProducts = [];

        foreach ($products as $i => $row) {
            if (!empty($row->product->product_category->name)) {
                $catProducts[$row->product->product_category->name]['qty'] = empty($catProducts[$row->product->product_category->name]['qty']) ? $row->total_expected : $catProducts[$row->product->product_category->name]['qty'] + $row->total_expected;
                $catProducts[$row->product->product_category->name]['product'][] = $row;
            }
        }

        return $catProducts;
    }

    public function getQuery($where, $query, $hospitalId)
    {
        if (!empty($hospitalId)) {
            $where = array_merge(array(['requisitions.hospital_id', $hospitalId]), $where);
        }

        if (!empty($query['requisition_no'])) {
            $where = array_merge(array(['requisitions.requisition_no', $query['requisition_no']]), $where);
        }

        if (!empty($query['status'])) {
            $where = array_merge(array(['requisitions.status', $query['status']]), $where);
        } else {
            $where = array_merge(array(['requisitions.status', '<>', 'INITIATING']), $where);
        }

        if (!empty($query['requisition_category_id'])) {
            $where = array_merge(array(['requisitions.requisition_category_id', $query['requisition_category_id']]), $where);
        }

        if (!empty($query['date_range'][0])) {
            $where = array_merge(array([DB::raw('DATE(requisitions.created_at)'), '>=', $query['date_range'][0]]), $where);
        }

        if (!empty($query['date_range'][1])) {
            $where = array_merge(array([DB::raw('DATE(requisitions.created_at)'), '<=', $query['date_range'][1]]), $where);
        }

        return $where;
    }

    /**
     * Departmental Requisition list
     *
     */
    public function deptIndex(Request $request)
    {
        $query = $request->query();
        $where = array();
        $where = array_merge(array(['requisitions.type', 2]), $where);
        $hospitalId = empty($query['hospital_id']) ? (!empty(Auth::user()->hospital_id) ? Auth::user()->hospital_id : '') : $query['hospital_id'];
        $departmentId = null;
        if (empty($query['hospital_id']) && empty($query['department_id'])) {
            $where = array_merge(array(['requisitions.department_id', Auth::user()->department_id]), $where);
            $departmentId = Auth::user()->department_id;
        } else if (!empty($query['department_id'])) {
            $where = array_merge(array(['requisitions.department_id', $query['department_id']]), $where);
            $departmentId = $query['department_id'];
        }

        $requisitionAccess = [];
        if (!empty(Auth::user()->role->name) && !empty(\config('settings.requisition_access_dept')[Auth::user()->role->name])) {
            $requisitionAccess = \config('settings.requisition_access_dept')[Auth::user()->role->name];
        }

        $where = $this->getQuery($where, $query, $hospitalId);

        $data = Requisition::with('items', 'items.product', 'category', 'hospital', 'department', 'user')
            ->where($where)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($data as $row) {
            $row->nextStatus = $this->getNextStatus($row);
            $row->statusAccess = $this->getAccess($row, $requisitionAccess);
            $row->editAccess = in_array($row->status, $requisitionAccess) && in_array(Auth::user()->role->name, ['SA', 'DA', 'SAdmin']);
        }

        /* Category wise product quantity */
        $catProducts = $this->getCatProduct($data, 2, $hospitalId, $departmentId);
        /* ****  ******  *****  ***** */

        $param['query'] = $query;
        $param['data'] = $data;
        $param['catProducts'] = $catProducts;
        $param['type'] = 2;
        $param['department'] = Department::with('hospital')->find(Auth::user()->department_id);
        $param['hospitals'] = Hospital::select(['name', 'id'])->with('departments')->get();
        $param['categories'] = ProductCategory::pluck('name', 'id');
        $param['status'] = \config('settings.req_status_dept');
        $param['canCreate'] = in_array(Auth::user()->role->name, ['DA', 'Admin', 'SAdmin']);

        return Inertia::render('requisition/index', ['param' => $param]);
    }

    /**
     * Create Requisition
     */
    public function create()
    {
        $param['categories'] = ProductCategory::select('id', 'name as text')->get();
        $param['products'] = Product::all();
        $param['requisition_category'] = null;

        return Inertia::render('requisition/create', ['param' => $param]);
    }

    /**
     * Create Requisition
     */
    public function deptCreate()
    {
        $param['categories'] = ProductCategory::select('id', 'name as text')->get();
        $param['products'] = Product::all();
        $param['requisition_category'] = null;
        $param['type'] = 2;

        return Inertia::render('requisition/create', ['param' => $param]);
    }

    public function new(Request $request)
    {
        $param['categories'] = ProductCategory::all();
        $param['products'] = Product::all();
        $param['requisition_category'] = ProductCategory::find($request->query('category_id'));

        $type = 1;
        if ($request->type == 2) {
            $reqData = Requisition::where('status', 'INITIATING')
                ->where('department_id', Auth::user()->department_id)
                ->where('requisition_category_id', $request->query('category_id'))
                ->first();

            $type = 2;
        } else {
            $reqData = Requisition::where('status', 'INITIATING')
                ->where('hospital_id', Auth::user()->hospital_id)
                ->where('requisition_category_id', $request->query('category_id'))
                ->first();
        }

        if (empty($reqData)) {
            $reqData = new Requisition();
            $reqData->user_id = Auth::user()->id;
            $reqData->hospital_id = Auth::user()->hospital_id;
            $reqData->department_id = $type == 2 ? Auth::user()->department_id : null;
            $reqData->type = $type;
            $reqData->expected_date = Carbon::now()->addDays(2);
            $reqData->requisition_category_id = $request->query('category_id');
            $reqData->save();

            $reqData->requisition_no = ($type == 1 ? 'SR-' : 'DR-') . sprintf("%06d", $reqData->id);
            $reqData->update();
        }

        $param['data'] = $reqData;

        return Redirect::route('requisitions.edit', [$reqData->id]);
    }

    public function addItem(Request $request)
    {
        Validator::make($request->all(), [
            'requisition_id' => ['required'],
            'product_id' => ['required'],
            'expected_quantity' => ['required'],
            'requisition_category_id' => ['required'],
        ])->validate();

        $product = Product::find($request->product_id);

        if ($product->crossROLLimit()) {
            return redirect()->back()->with('message', 'Sorry! Exceed ROL Limit.');
        }

        $reqData = Requisition::find($request->requisition_id);

        $itemData = RequisitionDetail::where('product_id', $request->product_id)->where('requisition_id', $reqData->id)->first();

        if ($itemData) {
            $itemData->expected_quantity += $request->expected_quantity;
            $itemData->update();
        } else {
            $itemData = new RequisitionDetail();
            $itemData->requisition_id = $reqData->id;
            $itemData->product_id = $request->product_id;
            $itemData->reorder_label = $product->reorder_label;
            $itemData->expected_quantity = $request->expected_quantity;
            $itemData->save();
        }
        return redirect()->back()->with('message', 'Item Added Successfully.');
    }

    /**
     * Destroy a Requisition's Item
     *
     * @param  $id
     */
    public function deleteItem(Request $request)
    {
        if ($request->has('id')) {
            $r = RequisitionDetail::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }

    /**
     * Cancel a Requisition's Item
     */
    public function cancelItem(Request $request)
    {
        if ($request->has('id')) {
            $r = RequisitionDetail::find($request->input('id'))->update(['status' => 'CANCELED']);
            return redirect()->back();
        }
    }

    /**
     * Update Requisition
     *
     * @param $request
     * @param  $id
     */
    public function updateItem(Request $request, $id)
    {
        if ($request->has('id')) {
            RequisitionDetail::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Requisition Detail Updated Successfully.');
        }
    }

    /**
     * Edit Requisition
     *
     * @param $id
     */
    public function edit(Requisition $requisition)
    {
        $param['categories'] = ProductCategory::all();
        $param['suppliers'] = Supplier::all();
        $param['requisition_category'] = ProductCategory::find($requisition->requisition_category_id);
        $param['products'] = Product::select('id', 'name as text')->where('product_category_id', $requisition->requisition_category_id)->get();

        $requisition->items = RequisitionDetail::where('requisition_id', $requisition->id)->with('product')->get();
        $requisition->nextStatus = $this->getNextStatus($requisition);

        $param['data'] = $requisition;
        $param['editConfig'] = $this->editConfig($requisition);
        $param['image'] = $requisition->images->last();
        $param['total_item'] = $requisition->items->count();
        $param['purchaseTypes'] = \config('settings.purchaseTypes');

        return Inertia::render('requisition/edit', ['param' => $param]);
    }

    public function editConfig($requisition)
    {
        $config = [];

        $config['priceOpen'] = $requisition->type == 1 ? (in_array($requisition->status, ['PROCESSING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED']) ? true : false) : false;
        $config['isDelivered'] = in_array($requisition->status, ['DELIVERED', 'RECEIVED']) ? true : false;
        $config['receiveOpen'] = in_array($requisition->status, ['DELIVERED', 'RECEIVED']) ? true : false;
        $config['returnOpen'] = in_array($requisition->status, ['RECEIVED']) ? true : false;
        $config['deleteOpen'] = in_array($requisition->status, ['INITIATING']) ? true : false;
        $config['cancelOpen'] = in_array($requisition->status, ['APPROVED_BY_GM', 'APPROVED_BY_DF', 'PROCESSING']) ? true : false;
        $config['approvedQtyOpen'] = in_array($requisition->status, ['APPROVED_BY_GM', 'APPROVED_BY_DF', 'PROCESSING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED']) ? true : false;
        $config['deliveredQtyOpen'] = in_array($requisition->status, ['PROCESSING', 'DELIVERED', 'LOCAL_PURCHASE', 'RECEIVED']) ? true : false;
        $config['processingOpen'] = in_array($requisition->status, ['PROCESSING', 'LOCAL_PURCHASE', 'APPROVED_BY_GM']) ? true : false;

        return $config;
    }

    public function getNextStatus($requisition)
    {
        $data['status_keys'] = $requisition->type == 1 ? array_keys(\config('settings.req_status')) :  array_keys(\config('settings.req_status_dept'));
        $data['status_values'] = $requisition->type == 1 ? array_values(\config('settings.req_status')) :  array_values(\config('settings.req_status_dept'));

        if ($requisition->status == 'LOCAL_PURCHASE') {
            return ['key' => 'RECEIVED', 'value' => \config('settings.req_status')['RECEIVED']];
        }

        for ($i = 0; $i < sizeof($data['status_keys']); $i++) {
            if ($data['status_keys'][$i] == $requisition->status && $requisition->status != 'RECEIVED') {
                return ['key' => $data['status_keys'][$i + 1], 'value' => $data['status_values'][$i + 1]];
            }
        }

        return null;
    }

    /**
     * Update Requisition
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, Requisition $requisition)
    {
        if ($request->has('id')) {
            $requisition = Requisition::find($request->input('id'));
            $requisition->update($request->except('items'));

            if (!empty($request->items)) {
                $itemModel = new RequisitionDetail();
                $itemModel->updateItems($requisition, $request->items);
            }

            if (isset($request->status) && $request->status == 'RECEIVED') {
                $requisition->receive_date = Carbon::now();
                $requisition->update();

                $inventory = new Inventory();
                $inventory->addInventory($request->input('id'), $requisition->type);

                DeliveredRequisition::dispatch($requisition);
            } else if (isset($request->status) && $request->status == 'DELIVERED') {
                foreach ($requisition->items as $item) {
                    $item->received_quantity = $item->delivered_quantity;
                    $item->update();
                }

                $requisition->delivered_date = Carbon::now();
                $requisition->update();

                DeliveredRequisition::dispatch($requisition);
            } else if (isset($request->status) && $request->status == 'PROCESSING') {
                foreach ($requisition->items as $item) {
                    $item->delivered_quantity = $item->approved_quantity;
                    $item->update();
                }

                return Redirect::route('requisitions.edit', [$request->input('id')]);
            } else if (isset($request->status) && $request->status == 'APPROVED_BY_GM') {
                foreach ($requisition->items as $item) {
                    $item->approved_quantity = $item->expected_quantity;
                    $item->update();
                }

                return redirect()->back()->with('message', 'Requisition Detail Updated Successfully.');
            } else if (isset($request->status) && $request->status == 'LOCAL_PURCHASE') {
                foreach ($requisition->items as $item) {
                    $item->approved_quantity = $item->expected_quantity;
                    $item->delivered_quantity = $item->approved_quantity;
                    $item->received_quantity = $item->delivered_quantity;
                    $item->update();
                }

                return redirect()->back()->with('message', 'Requisition Detail Updated Successfully.');
            }

            if ($requisition->type == 2) {
                if (isset($request->status) && $request->status == 'VERIFIED') {
                    foreach ($requisition->items as $item) {
                        $item->approved_quantity = $item->expected_quantity;
                        $item->update();
                    }

                    return redirect()->back()->with('message', 'Requisition Detail Updated Successfully.');
                }

                return Redirect::route('dept_requisitions')->with('message', 'Requisition Submitted Successfully.');
            }


            return Redirect::route('requisitions.index')->with('message', 'Requisition Submitted Successfully.');
        }
    }

    public function uploadImage(Request $request, Requisition $requisition)
    {
        if ($image = $request->file('file')) {
            try {
                DB::beginTransaction();

                $oldImage = $requisition->images->last();

                $filename = $image->store('public');
                $source = url(Storage::url($filename));

                $imageObj = new Image();
                $imageObj->source = $source;
                $imageObj->filename = $filename;
                $imageObj->object()->associate($requisition);
                $imageObj->save();

                if ($oldImage) {
                    Storage::delete($oldImage['filename']);
                    $oldImage->delete();
                }

                DB::commit();

                return redirect()->back()->with('message', 'Attachment uploaded successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }
    }

    /**
     * Destroy Requisition
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Requisition::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
