<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentConsumption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hospital_id', 'department_id', 'product_id', 'product_qty', 'product_for',
        'remarks', 'status', 'updated_by', 'created_by', 'consumption_room_id'
    ];

    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

    public function room(){
        return $this->belongsTo(ConsumptionRoom::class, 'consumption_room_id', 'id');
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }


}
