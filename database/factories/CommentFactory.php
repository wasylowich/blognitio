<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Blognitio\Post;
use Blognitio\User;
use Blognitio\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
		'user_id' => function () {
            return factory(User::class)->create()->id;
        },
		'post_id' => function () {
            return factory(Post::class)->create()->id;
        },
        'body'  => $faker->paragraph,
    ];
});
