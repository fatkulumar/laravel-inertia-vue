<?php

namespace App\Models;

use App\Traits\GenUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadOrganization extends Model
{
    use HasFactory, GenUid;

    protected $fillable = [
        'name',
        'status',
        'start_date',
        'end_date',
    ];
}
