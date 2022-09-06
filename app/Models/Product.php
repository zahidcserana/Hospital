<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class)->withTrashed();;
    }

    public function product_brand()
    {
        return $this->belongsTo(ProductBrand::class)->withTrashed();;
    }

    public function product_model()
    {
        return $this->belongsTo(ProductModel::class)->withTrashed();;
    }

    public function stockLimit()
    {
        return $this->reorder_label + ($this->reorder_label * config('settings.appSettings.stockLimit'));
    }

    public function requestLimit()
    {
        return  $this->stockLimit * config('settings.appSettings.requestLimit');
    }

    public function crossROLLimit()
    {
        return false;
    }
}
