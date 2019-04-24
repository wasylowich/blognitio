<?php

namespace Blognitio\Listeners;

use Blognitio\User;
use Blognitio\Events\CommentCreated;
use Illuminate\Support\Facades\Notification;
use Blognitio\Notifications\CommentCreated as CommentCreatedNotification;

class SendCommentCreatedNotification
{
    /**
     * Handle the event.
     *
     * @param  CommentCreated  $event
     * @return void
     */
    public function handle(CommentCreated $event)
    {
        $notifiableUsers = User::whereId($event->post->user_id)
            ->orWhereIn('id', $event->uniqueCommenterIds->all())
            ->get()
            ->filter(function ($notifiableUser) use ($event) {
                return $notifiableUser->id != $event->comment->user_id;
            });

        Notification::send($notifiableUsers, new CommentCreatedNotification($event->comment, $event->post, $event->commenter));
    }
}
