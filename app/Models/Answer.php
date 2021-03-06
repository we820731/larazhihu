<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    use Traits\VoteTrait;

    protected $guarded = ['id'];

    protected $appends = [
        'upVotesCount',
        'downVotesCount',
    ];

    protected static function boot()
    {
        parent::boot(); //

        static::created(function ($reply) {
            $reply->question->increment('answers_count');
        });
    }

    public function isBest()
    {
        return $this->id == $this->question->best_answer_id;
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
