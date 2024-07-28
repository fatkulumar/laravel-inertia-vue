<?php

namespace App\Models;

use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentFile extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'schedule_id',
        'name',
        'file',
    ];
}
