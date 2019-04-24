<?php

namespace Blognitio\Http\Controllers\Api;

use Blognitio\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Blognitio\Events\CommentCreated;
use Blognitio\Http\Requests\CommentRequest;

class PostCommentsController extends BaseApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Blognitio\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, Post $post)
    {
        list($comment, $uniqueCommenterIds) = DB::transaction(function () use ($request, $post) {
            $comment = $post->comments()->create([
                'user_id' => auth()->user()->id,
                'body'    => $request->body,
            ]);

            // Identify all the unique commenters on the post
            $uniqueCommenterIds = $post->comments->filter(function ($comment) use ($post) {
                return $comment->user_id != $post->user_id;
            })
            ->map(function ($comment) {
                return $comment->user_id;
            })
            ->unique();

            // If not already popular and sufficient commenters, the blogger becomes popular
            if ($post->blogger->isNotPopular()) {
                if ($uniqueCommenterIds->count() >= 5) {
                    $post->blogger->makePopular()->save();
                }
            }

            return [$comment, $uniqueCommenterIds];
        });

        event(new CommentCreated($comment, $post, auth()->user(), $uniqueCommenterIds));

        return response($comment, 201);
    }
}
