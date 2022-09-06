<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicineConsumption extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $guarded = [];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function room()
    {
        return $this->belongsTo(ConsumptionRoom::class, 'consumption_room_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
