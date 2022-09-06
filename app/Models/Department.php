<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hospital_id','name', 'dept_head_id', 'dept_admin_id', 'status', 'created_by', 'updated_by', 'is_for_doctor'
    ];

    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

    public function department_head(){
        return $this->belongsTo(Department::class, 'dept_head_id', 'id');
    }

    public function department_admin(){
        return $this->belongsTo(Department::class, 'dept_admin_id', 'id');
    }

}
