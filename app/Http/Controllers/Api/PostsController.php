<?php

namespace Blognitio\Http\Controllers\Api;


use Blognitio\Post;
use Illuminate\Support\Facades\DB;
use Blognitio\Http\Requests\BlogPostRequest;

class PostsController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->posts()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Blognitio\Http\Requests\BlogPostRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogPostRequest $request)
    {
        $dailyPostLimit = (int) config('blognitio.posts.daily_limit');

        $todaysPostCount = auth()->user()->posts()->today()->count();

        if ($todaysPostCount >= $dailyPostLimit) {
            return response('You have reached your daily limit of 5 blog posts', 403);
        }

        $post = DB::transaction(function () use ($request) {
            $post = auth()->user()->posts()->create($request->only(['title', 'body']));

            return $post;
        });

        return response($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Blognitio\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Blognitio\Http\Requests\BlogPostRequest  $request
     * @param  \Blognitio\Post                           $post
     * @return \Illuminate\Http\Response
     */
    public function update(BlogPostRequest $request, Post $post)
    {
        $post = DB::transaction(function () use ($request, $post) {
            $post->fill($request->only(['title', 'body']))->save();

            return $post;
        });

        return response($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Blognitio\Http\Requests\BlogPostRequest  $request
     * @param  \Blognitio\Post                           $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogPostRequest $request, Post $post)
    {
        $post = DB::transaction(function () use ($post) {
            $post->delete();

            return $post;
        });

        return response('', 204);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allPosts()
    {
        return Post::all();
    }
}
