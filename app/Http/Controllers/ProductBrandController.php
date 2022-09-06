<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductBrand::with('product_category')->get();

        return Inertia::render('settings/common/productBrand/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Product Brand';
        $data['categories'] = ProductCategory::all();

        return Inertia::render('settings/common/productBrand/create', ['data' => $data]);
    }

    /**
     * ProductBrand a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'product_category_id' => ['required'],
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();


        ProductBrand::create($request->all());

        return Redirect::route('product-brands.index')->with('message', 'Product Brand Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductBrand  $productBrand
     * @return \Illuminate\Http\Response
     */
    public function show(ProductBrand $productBrand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductBrand  $productBrand
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductBrand $productBrand)
    {
        $data = ProductBrand::find($productBrand->id);
        $data['title'] = 'Update Product Brand';
        $data['categories'] = ProductCategory::all();

        return Inertia::render('settings/common/productBrand/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductBrand  $productBrand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductBrand $productBrand)
    {
        Validator::make($request->all(), [
            'product_category_id' => ['required'],
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            ProductBrand::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Product Brand Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductBrand  $productBrand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            ProductBrand::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
