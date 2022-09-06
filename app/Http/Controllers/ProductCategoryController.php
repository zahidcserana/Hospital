<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductCategory::all();

        return Inertia::render('settings/common/productCategory/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title'] = 'New Product Category';

        return Inertia::render('settings/common/productCategory/create', ['data' => $data]);
    }

    /**
     * ProductCategory a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();


        ProductCategory::create($request->all());

        return Redirect::route('product-categories.index')->with('message', 'Product Category Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCategory $productCategory)
    {
        $data = ProductCategory::find($productCategory->id);
        $data['title'] = 'Update Product Category';

        return Inertia::render('settings/common/productCategory/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            ProductCategory::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Product Category Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            ProductCategory::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
