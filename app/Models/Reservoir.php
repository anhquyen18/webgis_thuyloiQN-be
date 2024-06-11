<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class Reservoir extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', /* các trường khác */];
    public function safetyReports()
    {
        return $this->hasMany(ReservoirSafety::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = IdGenerator::generate(['table' => 'reservoirs', 'field' => 'id', 'length' => 14, 'prefix' => 'reservoir-']);
        });
    }
}
