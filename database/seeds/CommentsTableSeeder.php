<?php

use Blognitio\User;
use Blognitio\Comment;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::with('posts')->get();

		$brian = $users->where('name', 'Brian')->first();
		$sam   = $users->where('name', 'Sam')->first();
		$goran = $users->where('name', 'Goran')->first();

		// Seed some comments on Brian's posts
		$postCount = 1;
		foreach ($brian->posts as $post) {
			if ($postCount == 1) {
				// The 1st post should have 4 comments from 4 users
				factory(Comment::class)->create(['user_id' => $sam->id, 'post_id' => $post->id]);
		        factory(Comment::class, 3)->create(['post_id' => $post->id]);
			} else {
				// The 2nd post should have 1 comment from another user
				factory(Comment::class)->create(['user_id' => $goran->id, 'post_id' => $post->id]);
			}

			$postCount++;
		}

		// Seed some comments on Sam's posts
		foreach ($sam->posts as $post) {
			factory(Comment::class)->create(['user_id' => $brian->id, 'post_id' => $post->id]);
		    factory(Comment::class, 2)->create(['post_id' => $post->id]);
		}

		// Seed some comments on Goran's posts
		foreach ($goran->posts as $post) {
			factory(Comment::class)->create(['user_id' => $sam->id, 'post_id' => $post->id]);
			factory(Comment::class)->create(['user_id' => $brian->id, 'post_id' => $post->id]);
		}
    }
}
