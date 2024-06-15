<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedTime extends Model
{
    use HasFactory;
    protected $table = 'locked_times';
    protected $fillable = [
        'start_time',
        'end_time',
    ];
}
