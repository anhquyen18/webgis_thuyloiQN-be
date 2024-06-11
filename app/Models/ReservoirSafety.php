<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\ObjectActivityDocument;
use Illuminate\Support\Facades\DB;

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
        $rootUrl = url('/api/safety-report/get-image/');
        $imageUrl = 'CONCAT(\'' . $rootUrl . '/\',';
        $docs = ObjectActivityDocument::where('object_activity_id', $reportId)->selectRaw("id,name," . $imageUrl . 'id) as rooturl')->get();

        return $docs;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = IdGenerator::generate(['table' => 'reservoir_safety', 'field' => 'id', 'length' => 20, 'prefix' => 'rsv-safety-']);
        });
    }
}
