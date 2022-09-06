<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\ProductModel;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductModelController extends Controller
{
    /**
     * product model list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductModel::with('product_category', 'product_brand')->get();

        return Inertia::render('settings/common/productModel/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Product Model';
        $data['categories'] = ProductCategory::all();
        $data['brands'] = ProductBrand::all();

        return Inertia::render('settings/common/productModel/create', ['data' => $data]);
    }

    /**
     * ProductModel a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'product_category_id' => ['required'],
            'product_brand_id' => ['required'],
            'status' => ['required'],
        ])->validate();


        ProductModel::create($request->all());

        return Redirect::route('product-models.index')->with('message', 'Product Model Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductModel  $productModel
     * @return \Illuminate\Http\Response
     */
    public function show(ProductModel $productModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductModel  $productModel
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductModel $productModel)
    {
        $data = ProductModel::find($productModel->id);
        $data['title'] = 'Update Product Model';
        $data['categories'] = ProductCategory::all();
        $data['brands'] = ProductBrand::all();

        return Inertia::render('settings/common/productModel/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductModel  $productModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductModel $productModel)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'product_category_id' => ['required'],
            'product_brand_id' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            ProductModel::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Product Model Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductModel  $productModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            ProductModel::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
