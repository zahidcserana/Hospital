<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getProducts()
    {
        $inventory = Inventory::where('quantity', '>', 0)->get();

        if (in_array(Auth::user()->role->name, ['DH', 'DA'])) {
            $inventory = $inventory->where('department_id', Auth::user()->department_id);
        }

        if (in_array(Auth::user()->role->name, ['SA', 'FD'])) {
            $inventory = $inventory->where('hospital_id', Auth::user()->hospital_id);
        }

        $product_ids = $inventory->pluck('product_id');

        $products = Product::whereIn('id', $product_ids)->get();

        return $products;
    }

    public function getMedicine($term = null)
    {
        $term = '%' . $term . '%';

        $products = Medicine::where('name', 'LIKE', $term)->limit(50)->get();

        return $products;
    }
}
