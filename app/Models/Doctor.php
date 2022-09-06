<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code', 'hospital_id', 'department_id', 'type', 'doctors_specialities_id', 'name',
        'bmdc_id', 'mobile', 'email', 'designation_id', 'institute_name', 'chamber_address'
        , 'photo', 'status', 'updated_by', 'created_by'
    ];


    public function designation(){
        return $this->belongsTo(Designation::class);
    }

    public function speciality(){
        return $this->belongsTo(DoctorsSpeciality::class, 'doctors_specialities_id', 'id');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }


}
