<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function admission()
    {
        return $this->hasMany(PatientAdmission::class);
    }

    public function opd_bills()
    {
        return $this->hasMany(OpdBilling::class);
    }

    public function ipd_admissions()
    {
        return $this->hasMany(PatientAdmission::class);
    }

    public function corporate_client()
    {
        return $this->belongsTo(CorporateClient::class);
    }

    public function generate_code(){
        $patient = Patient::find($this->id);
        $created = new Carbon($patient->created_at);
        $year_month = $created->format('ym');
        $patient_code = $year_month.str_pad($patient->id, 6, "0", STR_PAD_LEFT);
        $patient->code = $patient_code;
        $patient->save();
    }


}
