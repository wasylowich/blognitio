<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Blognitio\Post;
use Blognitio\User;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
		'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'title' => $faker->sentence,
        'body'  => $faker->paragraph,
    ];
});
