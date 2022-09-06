<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateClientDiscount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'corporate_client_id', 'hospital_id', 'service_id', 'service_package_id',
        'discount_percentage', 'discount_amount', 'discount_category', 'status', 'created_by', 'updated_by'
    ];

    public function hospital(){
        return $this->belongsTo(Hospital::class);
    }

    public function corporate_client(){
        return $this->belongsTo(CorporateClient::class);
    }

    public function service(){
        if($this->discount_category == 'OPDSERVICES'){
            return $this->belongsTo(OpdService::class, 'service_id', 'id');
        }
        return $this->belongsTo(IpdService::class, 'service_id', 'id');
    }

    public function service_package(){
        if($this->discount_category == 'OPDSERVICES'){
            return $this->belongsTo(OpdServicePackage::class, 'service_id', 'id');
        }
        return $this->belongsTo(IpdServicePackage::class, 'service_package_id', 'id');
    }



}
