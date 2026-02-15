<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrayerTime extends Model
{
    protected $fillable = [
        'date',
        'imsak',
        'subuh',
        'zuhur',
        'ashar',
        'maghrib',
        'isya',
    ];
}
