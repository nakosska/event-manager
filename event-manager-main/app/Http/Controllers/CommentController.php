<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function store(Request $request, Event $event)
    {
        $this->authorize('create', Comment::class);

        $validated = $request->validate([
            'body' => 'required|string|min:3|max:1000',
        ]);

        $comment = $event->comments()->create([
            'body' => $validated['body'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Комментарий добавлен!');
    }

    public function destroy(Event $event, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()->with('success', 'Комментарий удалён!');
    }
}
