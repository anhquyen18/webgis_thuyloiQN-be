<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'gender',
        'birthday',
        'phone_number',
        "department_id",
        "status_id",
    ];
    protected $appends = [
        'organization',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function setShouldOrganizationIdAttribute($value)
    {
        $this->shouldAppendCustomAttribute = $value;
    }

    public function getOrganizationAttribute()
    {
        if (is_null($this->department_id)) {
            return 'Không tìm thấy department_id hoặc chưa được gọi kèm';
        } else {
            $department = Department::find($this->department_id);
            $organization = Organization::find($department->organization_id);
            return $organization;
        }
    }


    public function policies()
    {

        return $this->belongsToMany(Policy::class, 'user_policies');
    }

    public function safetyReports()
    {
        return $this->hasMany(ReservoirSafety::class);
    }
}
