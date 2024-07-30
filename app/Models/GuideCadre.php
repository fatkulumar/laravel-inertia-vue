<?php

namespace App\Models;

use App\Traits\Acessor\ConverDateToIndonesia;
use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuideCadre extends Model
{
    use HasFactory, GenUid, ConverDateToIndonesia;

    protected $fillable = [
        'type_activity_id',
        'name',
        'link',
        'information',
    ];

    public function typeActivity()
    {
        return $this->belongsTo(TypeActivity::class, 'type_activity_id');
    }
}
