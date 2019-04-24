<?php

namespace Blognitio\Events;

use Blognitio\Post;
use Blognitio\User;
use Blognitio\Comment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
     * The collection of all users who have commented on the blog post
     *
     * @var \Illuminate\Support\Collection
     */
    public $uniqueCommenterIds;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, Post $post, User $commenter, $uniqueCommenterIds)
    {
        $this->comment            = $comment;
        $this->post               = $post;
        $this->commenter          = $commenter;
        $this->uniqueCommenterIds = $uniqueCommenterIds;
    }
}
