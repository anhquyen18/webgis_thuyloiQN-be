<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservoirSafety extends Model
{
    use HasFactory;
    protected $table = 'reservoir_safety';
    protected $fillable = [
        'name', 'reservoir_id', 'date_finished',
        'user_id', 'main_dam_status', 'main_dam_description',
        'spillway_status', 'spillway_description', 'monitor_system_status', 'monitor_system_description', 'finished_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservoir()
    {
        return $this->belongsTo(Reservoir::class);
    }
}
