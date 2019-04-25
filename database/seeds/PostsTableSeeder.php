<?php

use Blognitio\Post;
use Blognitio\User;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

		$brian = $users->where('name', 'Brian')->first();
		$sam   = $users->where('name', 'Sam')->first();
		$goran = $users->where('name', 'Goran')->first();


        factory(Post::class)->create(['user_id' => $brian->id, 'title' => "Brian's 1st post"]);
        factory(Post::class)->create(['user_id' => $brian->id, 'title' => "Brian's 2nd post"]);

        factory(Post::class)->create(['user_id' => $sam->id, 'title' => "Sam's 1st post"]);

        factory(Post::class)->create(['user_id' => $goran->id, 'title' => "Goran's 1st post"]);
    }
}
