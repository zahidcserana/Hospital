<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hospital_id','department_id', 'title', 'description', 'status', 'created_by', 'updated_by', 'is_for_doctor'
    ];


    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }


}
