<?php

namespace Blognitio;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token',
        'is_popular',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_popular'        => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Section for: Relation Methods
    |--------------------------------------------------------------------------
    |
    | Define all relation methods for the model here
    |
     */

    /**
     * One-to-many relations with the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * One-to-many-to-many relations with the Comment model through the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Post::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Section for: Getter Methods
    |--------------------------------------------------------------------------
    |
    | Define all getter methods for the model here
    |
     */

    /**
     * Getter for the is_popular attribute
     *
     * @return boolean
     */
    public function isPopular()
    {
        return $this->is_popular;
    }

    /**
     * Getter for the negated is_popular attribute
     *
     * @return boolean
     */
    public function isNotPopular()
    {
        return ! $this->isPopular();
    }

    /*
    |--------------------------------------------------------------------------
    | Section for: Setter Methods
    |--------------------------------------------------------------------------
    |
    | Define all setter methods for the model here
    |
     */

    /**
     * Setter for the is_popular attribute
     *
     * @return boolean
     */
    public function makePopular()
    {
        $this->is_popular = true;

        return $this;
    }
}
