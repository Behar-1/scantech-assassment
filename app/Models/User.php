<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_dispatch',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'can_dispatch' => 'boolean',
            'role' => \App\Enums\UserRole::class,
        ];
    }

    public function isDispatcher(): bool
    {
        return $this->role === \App\Enums\UserRole::DISPATCHER;
    }

    public function isSupervisor(): bool
    {
        return $this->role === \App\Enums\UserRole::SUPERVISOR;
    }

    public function isAdministrator(): bool
    {
        return $this->role === \App\Enums\UserRole::ADMINISTRATOR;
    }

    public function canDispatchTrips(): bool
    {
        return $this->can_dispatch
            && $this->role instanceof \App\Enums\UserRole
            && $this->role->canDispatch();
    }
}
