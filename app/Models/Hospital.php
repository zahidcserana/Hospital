<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code','name', 'address', 'district', 'contact', 'email', 'website_url','logo_image',
        'mobile_no', 'tnt_no', 'contact_person_name', 'contact_person_mobile', 'status', 'created_by', 'updated_by'
    ];

    public function stores() {
        return $this->hasMany(Store::class);
    }

    public function departments() {
        return $this->hasMany(Department::class);
    }

    public function corporate_client() {
        return $this->hasMany(CorporateClient::class);
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class);
    }

}
