<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }
    public function isParticipant()
    {
        return $this->role === 'participant';
    }

    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'event_user')->withPivot('registered_at', 'attended')->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }



}
