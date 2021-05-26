<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryAllWeather extends Model
{
    use HasFactory;
    protected $fillable = ['name','id_city','curr_temperat','min_temp','max_temp','description','pressure','humidity','wind','time_weather','icon'];
}
