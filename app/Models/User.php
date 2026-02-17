<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'structure_id',
        'balance',
        'salary',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getInitialsAttribute()
    {
        $words = preg_split('/\s+/', trim($this->name));

        if (count($words) === 1) {
            return mb_strtoupper(mb_substr($words[0], 0, 1));
        }

        return mb_strtoupper(
            mb_substr($words[0], 0, 1) .
            mb_substr($words[count($words) - 1], 0, 1)
        );
    }
    public function notifications(){
        return $this->hasMany(Notification::class);
    }
    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }
    public function leave(){
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }
}
