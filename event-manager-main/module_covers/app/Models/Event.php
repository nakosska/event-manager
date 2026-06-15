<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'cover_image', 'category_id', 'organizer_id', 'start_datetime', 'end_datetime', 'location', 'location_url', 'capacity', 'price', 'is_published'];
    protected $casts = ['start_datetime' => 'datetime', 'end_datetime' => 'datetime', 'is_published' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot('registered_at', 'attended')->withTimestamps();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isFull()
    {
        return !is_null($this->capacity) && $this->participants()->count() >= $this->capacity;
    }
    public function isUserRegistered($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }
    public function getAvailableSeats()
    {
        return is_null($this->capacity) ? null : max(0, $this->capacity - $this->participants()->count());
    }

}
