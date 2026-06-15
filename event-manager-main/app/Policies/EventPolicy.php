<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{

    public function viewAny(User $user): bool
    {

        return true;
    }


    public function view(User $user, Event $event): bool
    {

        if ($event->is_published) {
            return true;
        }

        return $user->isAdmin() || ($user->isOrganizer() && $user->id === $event->organizer_id);
    }

    public function create(User $user): bool
    {

        return $user->isOrganizer() || $user->isAdmin();
    }


    public function update(User $user, Event $event): bool
    {

        return $user->isAdmin() || ($user->isOrganizer() && $user->id === $event->organizer_id);
    }

    public function delete(User $user, Event $event): bool
    {

        return $user->isAdmin() || ($user->isOrganizer() && $user->id === $event->organizer_id);
    }


    public function register(User $user, Event $event): bool
    {

        if ($user->id === $event->organizer_id) {
            return false;
        }

        if ($event->isUserRegistered($user->id)) {
            return false;
        }

        if ($event->isFull()) {
            return false;
        }

        return $user->isParticipant() || $user->isOrganizer() || $user->isAdmin();
    }


    public function unregister(User $user, Event $event): bool
    {
        if (!$event->isUserRegistered($user->id)) {
            return false;
        }

        if ($event->start_datetime <= now()) {
            return false;
        }

        return true;
    }
}
