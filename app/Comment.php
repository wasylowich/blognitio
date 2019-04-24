<?php

namespace Blognitio;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'body'];

    /*
    |--------------------------------------------------------------------------
    | Section for: Relation Methods
    |--------------------------------------------------------------------------
    |
    | Define all relation methods for the model here
    |
     */

    /**
     * One-to-one relation with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commenter()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

   /**
     * One-to-one relation with the Post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
     public function post()
    {
    	return $this->belongsTo(Post::class);
    }
}
