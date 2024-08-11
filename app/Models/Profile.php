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
        'regency_regional_id',
        'profileable_id',
        'profileable_type',
        'address',
        'hp',
        'gender',
        'image',
    ];

    public function profileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    public function regencyRegional()
    {
        return $this->belongsTo(RegencyRegional::class, 'regency_regional_id', 'id');
    }
}
