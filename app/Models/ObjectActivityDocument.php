<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectActivityDocument extends Model
{
    use HasFactory;
    protected $table = 'object_activity_documents';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'object_activity_id',
        'name',
        'description',
    ];

    // public function activity()
    // {
    //     return $this->belongsTo(Department::class,'');
    // }
}
