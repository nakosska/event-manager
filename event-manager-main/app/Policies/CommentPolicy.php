<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{

    public function create(User $user): bool
    {
        return $user !== null;
    }


    public function delete(User $user, Comment $comment): bool
    {
        if ($user->id === $comment->user_id) {
            return true;
        }

        $event = $comment->event;
        if ($user->isOrganizer() && $user->id === $event->organizer_id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }
}
