<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentPolicy extends Model
{
    use HasFactory;

    protected $table = 'department_policies';

    protected $fillable = [
        'department_id',
        'policy_id',
    ];

    public $incrementing = false;
    public $timestamps = false;
    // public function department()
    // {

    //     return $this->belongsTo('App\Models\Department', 'department_id');
    // }

    // public function policies()
    // {

    //     return $this->belongsTo('App\Models\Policy');
    // }
}
