<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Notifications\YouWereInvited;
use App\Events\PublishQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublishedQuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Question $question)
    {
        $this->authorize('update', $question);

        $names = $question->invitedUsers();

        $question->publish();

        event(new PublishQuestion($question));

        return redirect("/questions/{$question->id}")->with('flash', "發佈成功！");
    }
}
