<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Blognitio\Post;
use Blognitio\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlogCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The user model
     *
     * @var /Blognitio/User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function an_api_consumer_can_create_a_blog_post()
    {
        $this->actingAs($this->user, 'api');

        $payload = [
            'title' => 'Test title',
            'body'  => 'Test body',
        ];

        $response = $this->json('POST', route('api.posts.store'), $payload)
            ->assertStatus(201)
            ->assertJson([
                'user_id' => $this->user->id,
                'title'   => $payload['title'],
                'body'    => $payload['body'],
            ]);
    }

    /** @test */
    public function an_api_consumer_can_view_a_list_of_all_blog_posts()
    {
        $user2 = factory(User::class)->create();

        $post1 = factory(Post::class)->create(['user_id' => $this->user->id]);
        $post2 = factory(Post::class)->create(['user_id' => $user2->id]);

        $this->actingAs($this->user, 'api');

        $response = $this->json('GET', route('api.allPosts.index'))
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJson([
                ['user_id' => $this->user->id],
                ['user_id' => $user2->id],
            ]);
    }

    /** @test */
    public function an_api_consumer_can_view_a_list_of_own_blog_posts()
    {
        $user2 = factory(User::class)->create();

        $post1 = factory(Post::class)->create([
            'user_id' => $this->user->id,
            'title'   => 'Test title',
            'body'    => 'Test body',
        ]);

        $post2 = factory(Post::class)->create(['user_id' => $user2->id]);

        $this->actingAs($this->user, 'api');

        $response = $this->json('GET', route('api.posts.index'))
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'user_id' => $this->user->id,
                    'title'   => $post1->title,
                    'body'    => $post1->body,
                ],
            ]);
    }

    /** @test */
    public function an_api_consumer_can_view_a_blog_post()
    {
        $this->actingAs($this->user, 'api');

        $post = factory(Post::class)->create(['user_id' => $this->user->id]);

        $response = $this->json('GET', route('api.posts.show', $post))
            ->assertStatus(200)
            ->assertJson([
                'user_id' => $this->user->id,
                'title'   => $post->title,
                'body'    => $post->body,
            ]);
    }

    /** @test */
    public function an_api_consumer_can_update_a_blog_post()
    {
        $this->actingAs($this->user, 'api');

        $post = factory(Post::class)->create(['user_id' => $this->user->id]);

        $payload = [
            'title' => 'Updated title',
            'body'  => 'Updated body',
        ];

        $this->assertNotEquals($payload['title'], $post->title);
        $this->assertNotEquals($payload['body'], $post->body);

        $response = $this->json('PATCH', route('api.posts.update', $post), $payload)
            ->assertStatus(200)
            ->assertJson([
                'title'   => $payload['title'],
                'body'    => $payload['body'],
            ]);

        $post = $post->fresh();

        $this->assertEquals($payload['title'], $post->title);
        $this->assertEquals($payload['body'], $post->body);
    }

    /** @test */
    public function an_api_consumer_can_delete_a_blog_post()
    {
        $this->actingAs($this->user, 'api');

        $post = factory(Post::class)->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('api.posts.destroy', $post))
            ->assertStatus(204);

        $this->assertTrue($post->fresh()->trashed());
    }

    /**
     * *************************************************************************
     *
     * Negative Tests
     *
     * *************************************************************************
     */

    /** @test */
    public function an_api_consumer_cannot_create_a_blog_post_without_required_fields()
    {
        $this->actingAs($this->user, 'api');

        $payload = [];

        $response = $this->post(route('api.posts.store'), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'body']);
    }

    /** @test */
    public function an_api_consumer_cannot_update_a_blog_post_without_required_fields()
    {
        $this->actingAs($this->user, 'api');

        $post = factory(Post::class)->create(['user_id' => $this->user->id]);

        $payload = [];

        $response = $this->patch(route('api.posts.update', $post), $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'body']);
    }

    /** @test */
    public function an_api_consumer_cannot_update_a_blog_post_belonging_to_another_blogger()
    {
        $this->actingAs($this->user, 'api');

        $anotherBlogger = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $anotherBlogger->id]);

        $payload = [
            'title' => 'Updated title',
            'body'  => 'Updated body',
        ];

        $response = $this->patch(route('api.posts.update', $post), $payload)
            ->assertStatus(403);
    }

    /** @test */
    public function an_api_consumer_cannot_delete_a_blog_post_belonging_to_another_blogger()
    {
        $this->actingAs($this->user, 'api');

        $anotherBlogger = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $anotherBlogger->id]);

        $response = $this->delete(route('api.posts.destroy', $post))
            ->assertStatus(403);
    }

    /** @test */
    public function an_api_consumer_cannot_create_more_than_the_daily_limit_of_blog_posts()
    {
        config(['blognitio.posts.daily_limit' => 5]);

        $dailyPostLimit = (int) config('blognitio.posts.daily_limit');

        $posts = factory(Post::class, $dailyPostLimit)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user, 'api');

        $payload = [
            'title' => 'Test title',
            'body'  => 'Test body',
        ];

        $response = $this->json('POST', route('api.posts.store'), $payload)
            ->assertStatus(403);

        $this->assertEquals('You have reached your daily limit of 5 blog posts', $response->getContent());
    }
}
