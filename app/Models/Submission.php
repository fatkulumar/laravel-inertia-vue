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
        'chief_id', //ketua pelaksana
        'type_activity_id', //jenis kegiatan
        'periode',
        'poster', //konsep kegiatan
        'concept', //konsep kegiatan
        'committee_layout', //susunan panitia
        'target_participant', //target peserta
        'speaker', //pemateri
        'total_activity', // total kegiatan yang sudah dikerjakan
        'price', // harga
        'facility', // fasiliitas
        'total_rooms_stay', // jumlah ruang menginap
        'benefit', // jumlah ruang menginap
        'location',
        'google_maps',
        'address',
        'status',
        'start_date_class',
        'end_date_class',
        'approval_date',
        'graduation_date',
        'proposal',
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
