<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
        'organization_id',
    ];

    public function policies()
    {

        return $this->belongsToMany(Policy::class, 'department_policies');
        // return $this->belongsToMany('App\Models\Policy', 'department_policies', 'department_id', 'policy_id')->where('department_id', $this->id);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organization()
    {

        return $this->belongsTo(Organization::class,);
    }
}
