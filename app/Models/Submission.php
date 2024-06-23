<?php

namespace App\Models;

use App\Traits\Acessor\ConverDateToIndonesia;
use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'participant_id',
        'committee_id',
        'category_id',
        'class_room_id',
        'periode',
        'location',
        'google_maps',
        'address',
        'status',
        'start_date_class',
        'end_date_class',
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

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'participant_id', 'id');
    // }
}
