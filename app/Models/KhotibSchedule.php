<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhotibSchedule extends Model
{
    protected $fillable = [
        'khotib_name',
        'bilal',
        'khutbah_dates',
        'notes',
    ];

    protected $casts = [
        'khutbah_dates' => 'array',
    ];
}
