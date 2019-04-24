<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route Group for: Non-Versioned API (for Mybanker5 consumption)
Route::middleware(['auth:api'])
    ->namespace('Api')
    ->as('api.')
    ->group(function () {

	// CRUD for posts
    Route::get('all-posts', 'PostsController@allPosts')->name('allPosts.index');
    Route::resource('posts', 'PostsController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

	// CRUD for comments
    Route::post('posts/{post}/comments', 'PostCommentsController@store')->name('posts.comments.store');
});
