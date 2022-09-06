<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_category_id', 'product_brand_id', 'name', 'description', 'status',
        'created_by', 'updated_by'
    ];


    public function product_brand(){
        return $this->belongsTo(ProductBrand::class);
    }

    public function product_category(){
        return $this->belongsTo(ProductCategory::class);
    }

}
