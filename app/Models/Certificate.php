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
        'certificateable_id', //id_user
        'certificateable_type',
        'credential_id',
        'expired_at',
    ];

     public function certificateable(): MorphTo
    {
        return $this->morphTo();
    }
}
