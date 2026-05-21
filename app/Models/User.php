<?php

namespace App\Models;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy([UserObserver::class])]
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
        'uuid',
        'name',
        'email',
        'password',
        'structure_id',
        'is_return',
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

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(PayRoll::class);
    }

    public function managedStructure()
    {
        return $this->hasOne(Structure::class, 'manager_id');
    }

    public function hasLeaveApprover()
    {

        if ($this->hasRole('Employee')) {
            return true;
        }


        $managed = $this->managedStructure;
        if ($managed) {
            $structure = $managed->parent;
            while ($structure) {
                if ($structure->manager_id && $structure->manager_id !== $this->id) {
                    return true;
                }
                $structure = $structure->parent;
            }
        }

        return false;
    }

    public function isChiefExecutive()
    {
        $isInHierarchy = $this->structure !== null || $this->managedStructure !== null;

        return $isInHierarchy && ! $this->hasLeaveApprover();
    }
}
