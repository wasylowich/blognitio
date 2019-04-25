<?php

use Blognitio\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Create Brian
		$brian = factory(User::class)->create(['name' => 'Brian', 'api_token' => 'b-secret']);

    	// Create Sam
		$sam   = factory(User::class)->create(['name' => 'Sam', 'api_token' => 's-secret']);

    	// Create Goran
		$goran = factory(User::class)->create(['name' => 'Goran', 'api_token' => 'g-secret']);

		// Create some random users
		factory(User::class, 5)->create();
    }
}
