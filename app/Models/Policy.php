<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function departments()
    {
        // return $this->belongsToMany('App\Models\Department', 'department_policies', 'policy_id', 'department_id');
        // return $this->belongsToMany(Policy::class);
        return $this->belongsToMany(Department::class, 'department_policies')->withTimestamps(false);
    }
}
