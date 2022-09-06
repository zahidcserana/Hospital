<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'source',
        'filename'
    ];

    public function object()
    {
        return $this->morphTo();
    }
}
