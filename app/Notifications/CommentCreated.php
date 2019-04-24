<?php

namespace Blognitio\Notifications;

use Blognitio\Post;
use Blognitio\User;
use Blognitio\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommentCreated extends Notification
{
    use Queueable;

    /**
     * The comment
     *
     * @var \Blognitio\Comment
     */
    public $comment;

    /**
     * The post
     *
     * @var \Blognitio\Post
     */
    public $post;

    /**
     * The commenter
     *
     * @var \Blognitio\User
     */
    public $commenter;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, Post $post, User $commenter)
    {
        $this->comment   = $comment;
        $this->post      = $post;
        $this->commenter = $commenter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting('Hello!')
                    ->line("A new comment from user: {$this->commenter->name} was created on the blog post with title:")
                    ->line($this->post->title);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
