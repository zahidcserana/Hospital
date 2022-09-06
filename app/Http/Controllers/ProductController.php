<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\ProductModel;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * product model list
     */
    public function index()
    {
        $data = Product::with('product_category')->get();

        return Inertia::render('settings/common/product/index', ['data' => $data]);
    }

    /**
     * Show Product.
     */
    public function create()
    {
        $data['title'] = 'New Product';
        $data['categories'] = ProductCategory::all();
        $data['brands'] = ProductBrand::all();
        $data['models'] = ProductModel::all();

        return Inertia::render('settings/common/product/create', ['data' => $data]);
    }

    /**
     * Store Product
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'product_category_id' => ['required'],
            'product_brand_id' => ['required'],
            'product_model_id' => ['required'],
            'status' => ['required'],
        ])->validate();

        $data = $request->all();

        $data['code'] = mt_rand(100000, 999999);

        Product::create($data);

        return Redirect::route('products.index')->with('message', 'Product Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductModel  $product
     * @return \Illuminate\Http\Response
     */
    public function show(ProductModel $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductModel  $product
     */
    public function edit(ProductModel $product)
    {
        $data = Product::find($product->id);
        $data['title'] = 'Update Product';
        $data['categories'] = ProductCategory::all();
        $data['brands'] = ProductBrand::all();
        $data['models'] = ProductModel::all();

        return Inertia::render('settings/common/product/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductModel  $product
     */
    public function update(Request $request, ProductModel $product)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'product_category_id' => ['required'],
            'product_brand_id' => ['required'],
            'product_model_id' => ['required'],
            'status' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            Product::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Product Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductModel  $product
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Product::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
