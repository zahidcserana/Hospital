<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'bed_number'
    ];


}
