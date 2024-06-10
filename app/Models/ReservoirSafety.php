<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\ObjectActivityDocument;

class ReservoirSafety extends Model
{
    use HasFactory;
    protected $table = 'reservoir_safety';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['docs'];
    protected $fillable = [
        'id',
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

    public function getDocsAttribute()
    {
        // $reportId = $this->id;
        $reportId = $this->id;

        $docs = ObjectActivityDocument::where('object_activity_id', $reportId)->select('name')->get();

        return [
            'docs' => $docs
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = IdGenerator::generate(['table' => 'reservoir_safety', 'field' => 'id', 'length' => 20, 'prefix' => 'rsv-safety-']);
        });
    }
}
