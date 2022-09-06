<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(RequisitionDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'requisition_category_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'object');
    }
}
