<?php

namespace App\Models;

use App\Traits\Acessor\ConverDateToIndonesia;
use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Certificate extends Model
{
    use HasFactory, GenUid, ConverDateToIndonesia;

    protected $fillable = [
        'submission_id',
        'head_organization_id',
        'user_id',
        'credential_id',
        'expired_at',
        'image',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function headOrganization()
    {
        return $this->belongsTo(HeadOrganization::class, 'head_organization_id');
    }
}
