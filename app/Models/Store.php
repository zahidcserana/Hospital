<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code', 'hospital_id', 'department_id', 'name', 'description', 'status', 'created_by', 'updated_by'
    ];


    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }


}
