<?php

namespace App\Models;

use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegencyRegional extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'regional_id',
        'regency',
    ];

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
