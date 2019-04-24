<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Blognitio\Post;
use Blognitio\User;
use Blognitio\Comment;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Blognitio\Notifications\CommentCreated as CommentCreatedNotification;

class BlogCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_api_consumer_can_comment_on_another_users_blog_post()
    {
        $blogger   = factory(User::class)->create();
        $commenter = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $blogger->id]);

        $this->actingAs($commenter, 'api');

        $payload = [
            'body' => 'Test comment body',
        ];

        $response = $this->json('POST', route('api.posts.comments.store', $post), $payload)
            ->assertStatus(201)
            ->assertJson([
                'user_id' => $commenter->id,
                'body'    => $payload['body'],
            ]);
    }

    /** @test */
    public function an_api_consumer_will_be_marked_popular_if_a_blog_post_has_5_or_more_unique_commenters()
    {
        $blogger   = factory(User::class)->create();
        $commenter = factory(User::class)->create();

        $post     = factory(Post::class)->create(['user_id' => $blogger->id]);
        $comments = factory(Comment::class, 4)->create(['post_id' => $post->id]);

        $this->assertTrue($blogger->isNotPopular());

        $this->actingAs($commenter, 'api');

        $payload = [
            'body' => 'Test comment body',
        ];

        $response = $this->json('POST', route('api.posts.comments.store', $post), $payload)
            ->assertStatus(201)
            ->assertJson([
                'user_id' => $commenter->id,
                'body'    => $payload['body'],
            ]);

        $blogger = $blogger->fresh();

        $this->assertTrue($blogger->isPopular());
    }

    /** @test */
    public function an_api_consumer_will_not_be_marked_popular_if_a_blog_post_has_5_or_more_comments_from_less_than_5_unique_commenters()
    {
        $blogger   = factory(User::class)->create();
        $commenter = factory(User::class)->create();

        $post     = factory(Post::class)->create(['user_id' => $blogger->id]);
        $comments = factory(Comment::class, 3)->create(['post_id' => $post->id]);

        $forthComment = factory(Comment::class)->create(['user_id' => $commenter->id, 'post_id' => $post->id]);

        $this->assertTrue($blogger->isNotPopular());

        $this->actingAs($commenter, 'api');

        $payload = [
            'body' => 'Test comment body',
        ];

        $response = $this->json('POST', route('api.posts.comments.store', $post), $payload)
            ->assertStatus(201)
            ->assertJson([
                'user_id' => $commenter->id,
                'body'    => $payload['body'],
            ]);

        $blogger = $blogger->fresh();

        $this->assertFalse($blogger->isPopular());
    }

    /** @test */
    public function blogger_and_commenters_are_notified_by_email_when_a_comment_is_created_on_a_blog_post()
    {
        Notification::fake();

        $blogger           = factory(User::class)->create();
        $previousCommenter = factory(User::class)->create();
        $activeCommenter   = factory(User::class)->create();

        $post     = factory(Post::class)->create(['user_id' => $blogger->id]);
        $comment1 = factory(Comment::class)->create(['user_id' => $previousCommenter->id, 'post_id' => $post->id]);
        $comment2 = factory(Comment::class)->create(['user_id' => $activeCommenter->id, 'post_id' => $post->id]);

        $this->actingAs($activeCommenter, 'api');

        $payload = [
            'body' => 'Test comment body',
        ];

        $response = $this->json('POST', route('api.posts.comments.store', $post), $payload)
            ->assertStatus(201);

        // Assert a notification was sent to the blogger
        Notification::assertSentTo(
            [$blogger], CommentCreatedNotification::class, function ($notification, $channels) {
                return in_array('mail', $channels);
            }
        );

        // Assert a notification was sent to a previous commenter
        Notification::assertSentTo(
            [$previousCommenter], CommentCreatedNotification::class, function ($notification, $channels) {
                return in_array('mail', $channels);
            }
        );

        // Assert a notification was not sent to the active commenter
        Notification::assertNotSentTo(
            [$activeCommenter], CommentCreatedNotification::class, function ($notification, $channels) {
                return in_array('mail', $channels);
            }
        );
    }
}
