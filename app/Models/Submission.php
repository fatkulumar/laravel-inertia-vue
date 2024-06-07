<?php

namespace App\Models;

use App\Traits\Acessor\ConverDateToIndonesia;
use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory, GenUid, ConverDateToIndonesia;

    protected $fillable = [
        'participant_id',
        'committee_id',
        'status',
        'approval_date',
        'graduation_date',
        'file',
    ];

    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function committee()
    {
        return $this->belongsTo(User::class, 'committee_id');
    }
}
