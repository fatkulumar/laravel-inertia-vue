<?php

namespace App\Models;

use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Certificate extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'submission_id',
        'head_organization_id',
        'user_id',
        'credential_id',
        'expired_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function headOrganization()
    {
        return $this->hasOne(HeadOrganization::class, 'id', 'head_organization_id');
    }
}
