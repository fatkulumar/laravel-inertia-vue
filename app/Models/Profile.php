<?php

namespace App\Models;

use App\Traits\Acessor\ConverDateToIndonesia;
use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Profile extends Model
{
    use HasFactory, GenUid, ConverDateToIndonesia;

    protected $fillable = [
        'regional_id',
        'profileable_id',
        'profileable_type',
        'address',
        'hp',
        'gender',
    ];

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }
}
