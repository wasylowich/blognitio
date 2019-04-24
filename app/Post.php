<?php

namespace Blognitio;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'body'];

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
    public function blogger()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * One-to-many relations with the Comment model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
    	return $this->hasMany(Comment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Section for: Scope Methods
    |--------------------------------------------------------------------------
    |
    | Define all scope methods for the model here
    |
     */

    /**
     * Filters on posts that were created today
     *
     * @param  Illuminate\Database\Eloquent\Builder $builder
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($builder)
    {
        return $builder->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('created_at', '<=', Carbon::now()->endOfDay());
    }
}
