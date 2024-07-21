<?php

namespace App\Models;

use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'schedule_id',
        'participant_id',
        'proof',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

     public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
}
