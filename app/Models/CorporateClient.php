<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_name', 'address', 'contact_person_name', 'contact_person_mobile', 'contact_person_email',
        'status', 'created_by', 'updated_by'
    ];

    public function discounts() {
        return $this->hasMany(CorporateClientDiscount::class);
    }

    /**
     * Check billing item discount for corporate client
     *
     */
    public function checkDiscount($bill_type, $item_type, $item_id)
    {
        $discounts = $this->discounts;
        $discount = '';

        if($bill_type == 'OPD') {
            $discounts = $discounts->where('discount_category', 'OPDSERVICES');
        }else{
            $discounts = $discounts->where('discount_category', 'IPDSERVICES');
        }

        if($item_type == 'SERVICE') {
            $discount = $discounts->where('service_id', $item_id)->first();
        }
        if ($item_type == 'PACKAGE'){
            $discount = $discounts->where('service_package_id', $item_id)->first();
        }

        return $discount;

    }

}
